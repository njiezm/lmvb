<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public const SUBJECTS = [
        'Licence / inscription',
        'Compétitions & résultats',
        'Beach-volley',
        'Arbitrage',
        'Sélections',
        'Partenariat',
        'Presse',
        'Autre',
    ];

    public function create(Request $request)
    {
        $clubs = Club::active()->whereNotNull('ffvb_number')->orderBy('name')->get(['id', 'name']);

        return view('contact.create', [
            'clubs' => $clubs,
            'subjects' => self::SUBJECTS,
            'selectedClub' => $request->integer('club') ?: null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:150',
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9 +().-]{8,20}$/'],
            'club_id' => ['nullable', Rule::exists('clubs', 'id')->where('active', true)],
            'subject' => 'required|string|max:150',
            'message' => 'required|string|min:10|max:5000',
            'website' => 'prohibited', // pot de miel anti-robots
        ]);
        unset($data['website']);

        $contact = Contact::create($data + ['ip_address' => $request->ip()]);

        // Notification par email au secrétariat (silencieuse en cas d'échec d'envoi).
        try {
            Mail::raw(
                "Nouveau message depuis le site LMVB\n\nDe : {$contact->name} <{$contact->email}>\nSujet : {$contact->subject}\n\n{$contact->message}",
                fn ($m) => $m->to(setting('email'))->replyTo($contact->email, $contact->name)->subject('[Site LMVB] '.$contact->subject)
            );
        } catch (\Throwable $e) {
            Log::warning('Email de contact non envoyé', ['error' => $e->getMessage()]);
        }

        return redirect()->route('contact.create')
            ->with('success', 'Merci ! Votre message a bien été envoyé, nous vous répondrons rapidement.');
    }
}
