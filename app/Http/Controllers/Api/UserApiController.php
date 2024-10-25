<?php

namespace App\Http\Controllers\Api;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\PasswordResetMail;
use App\Mail\UserConfirmationMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class UserApiController extends Controller
{
    // Login method
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid login details'], 401);
        }

        if (Auth::user()->email_verified_at === null) {
            return response()->json(['error' => 'Please verify your email'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'role' => $user->role,
                'current_team_id' => $user->current_team_id,
            ],
            'token' => $token,
        ]);
    }

    // Logout method
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->tokens()->delete(); // Ensure all tokens are cleared
        }
        return response()->json(['message' => 'Logged out'], 200);
    }

    // Fetch authenticated user data
    public function user(Request $request)
    {
        return response()->json($request->user()->load('currentTeam'));
    }

    // View user profile
    public function viewUser()
    {
        $user = Auth::user()->load('currentTeam');
        return response()->json($user);
    }

    // Update user profile
    public function updateUser(Request $request)
    {
        $user = Auth::user();

        // Validate the input
        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
            'current_team_id' => 'sometimes|exists:teams,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Update the user fields
        $user->update($request->only(['first_name', 'last_name', 'email', 'current_team_id']));

        // Check if password needs to be updated
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    // Delete user profile
    public function deleteUser()
    {
        $user = Auth::user();
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    // Register new user
    public function register(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|same:confirm_password',
            'recaptcha' => 'required|string',
            'confirm_password' => 'required|string|min:8',
            'team_slug' => 'required|exists:teams,language_code',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Verify reCAPTCHA if environment is not local
        if (env('APP_ENV') !== 'local') {
            $recaptchaResponse = $request->input('recaptcha');
            $recaptchaSecret = env('RECAPTCHA_SECRET_KEY');
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $recaptchaSecret,
                'response' => $recaptchaResponse,
            ]);

            if (!$response->json('success')) {
                return response()->json(['error' => 'reCAPTCHA verification failed'], 422);
            }
        }
        $current_team = Team::where('language_code', $request->team_slug)->first();
        // Create user with default role as contestant and email verification token
        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'contestant',
        ]);

        $user->save();

        // Add the user to the specified team
        $user->teams()->attach($current_team->id);

        $user->current_team_id = $current_team->id;
        $user->email_verification_token = Str::random(32);
        $user->save();

        // Generate the verification URL
        $verificationUrl = url($request->site_url . '/verify-email?token=' . $user->email_verification_token);

        // Send the confirmation email
        Mail::to($user->email)->send(new UserConfirmationMail($user, $verificationUrl));

        // Generate an authentication token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // Verify user email
    public function verifyEmail(Request $request)
    {
        $user = User::where('email_verification_token', $request->token)->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid verification token'], 400);
        }

        $user->email_verified_at = now();
        $user->email_verification_token = null;
        $user->save();

        return response()->json(['message' => 'Email successfully verified'], 200);
    }

    // Set user notifications
    public function setNotifications(Request $request)
    {
        $user = Auth::user();

        // Add logic for handling notifications settings
        $user->notifications_enabled = $request->input('notifications_enabled', false);

        $user->save();

        return response()->json(['message' => 'Notification settings updated']);
    }

    // Send reset link email
    public function sendResetLinkEmail(Request $request)
    {
        // Validate the email
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Find the user
        $user = User::where('email', $request->email)->first();

        // Generate the password reset token
        $token = Password::createToken($user);

        // Create the reset URL
        $resetUrl = url("/reset-password?token={$token}&email={$user->email}");

        // Send the reset email
        Mail::to($user->email)->send(new PasswordResetMail($user, $resetUrl));

        return response()->json(['message' => 'Password reset link sent successfully.'], 200);
    }
}
