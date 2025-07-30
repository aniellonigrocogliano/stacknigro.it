<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HeroSetting;
use App\Models\Skill;
use App\Models\Bio;
use App\Models\Project;
use App\Models\Contact;
use App\Models\Message;
use Illuminate\Support\Facades\Mail;

class FrontendController extends Controller
{
    public function index()
    {
        $hero = HeroSetting::first();
        $skills = Skill::all();
        $bio = Bio::first();
        $projects = Project::inRandomOrder()->take(6)->get();
        $contact = Contact::first();

        return view('frontend.index', compact('hero', 'skills', 'bio', 'projects', 'contact'));
    }

    public function sendMessage(Request $request)
{
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'required|string|max:20',
        'message' => 'required|string',
    ]);

    // Salvataggio nel database
    $message = Message::create([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'phone' => $request->phone,
        'message' => $request->message,
    ]);

    // Recupera l'email dalla tabella contacts
    $recipient = Contact::first()?->email ?? env('MAIL_FROM_ADDRESS');

    // Invia email
    Mail::raw("Nuovo messaggio da {$message->first_name} {$message->last_name} ({$message->email}, {$message->phone}):\n\n{$message->message}", function ($mail) use ($recipient) {
        $mail->to($recipient)
             ->subject('Nuovo messaggio dal sito');
    });

    return redirect()->back()->with('success', 'Messaggio inviato con successo!');
}
}
