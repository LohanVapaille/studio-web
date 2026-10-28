<?php

namespace App\Http\Controllers;

use App\Mail\NewContactMessage;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        try {
            $message = Message::create($validated);
            Mail::to('lohanvapaille@gmail.com')->send(new NewContactMessage($message));

            return back()->with('success', 'Votre message a bien été envoyé, je reviens vers vous rapidement.');
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue, merci de réessayer ou de me contacter directement par mail.');
        }
    }
}