<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'comments' => 'required|string',
            'recaptcha' => 'required|string',
        ]);

        if (env('APP_ENV') !== 'local') {
            $recaptchaResponse = $request->input('recaptcha');
            $recaptchaSecret = env('RECAPTCHA_SECRET_KEY');
            $response = Http::post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $recaptchaSecret,
                'response' => $recaptchaResponse,
            ]);

            if (!$response->json('success')) {
                return response()->json(['error' => 'reCAPTCHA verification failed'], 422);
            }
        }

        // Store the contact associated with the user's team
        $contact = Contact::create(array_merge($validated, [
            'team_id' => $user->current_team_id,
        ]));

        // Send email to admin
        Mail::to(config('mail.admin_email'))->send(new \App\Mail\ContactMail($contact));

        return response()->json(['message' => 'Contact saved and email sent.'], 201);
    }
}
