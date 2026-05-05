<?php

namespace App\Http\Controllers\Api;

use App\Models\Team;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Outl1ne\NovaSettings\NovaSettings;

class ContactApiController extends Controller
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
            'team' => 'required|exists:teams,link',
        ]);

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

        $team = Team::where('link', $validated['team'])->first();
        // Store the contact associated with the user's team
        $contact = Contact::create(array_merge($validated, [
            'team_id' => $team->id
        ]));

        $email1 = DB::table('nova_settings')->where('key', 'email_1')->where('team_id', $team->id)->value('value');
        $email2 = DB::table('nova_settings')->where('key', 'email_2')->where('team_id', $team->id)->value('value');

        $recipients = array_filter([$email1, $email2], fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL));

        if (empty($recipients)) {
            $recipients = [config('mail.admin_email')];
        }

        Mail::to($recipients)->send(new \App\Mail\ContactMail($contact));

        return response()->json(['message' => 'Contact saved and email sent.'], 201);
    }
}
