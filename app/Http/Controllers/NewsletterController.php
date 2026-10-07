<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'newsletter_email' => 'required|email|max:150',
            'website' => 'prohibited',
        ], [], ['newsletter_email' => 'adresse email']);

        $subscriber = NewsletterSubscriber::firstOrNew(['email' => mb_strtolower($data['newsletter_email'])]);
        $subscriber->token ??= Str::random(48);
        $subscriber->unsubscribed_at = null;
        $subscriber->save();

        return back()->with('newsletter', 'Inscription confirmée : vous recevrez les alertes de la ligue.');
    }

    public function unsubscribe(string $token)
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();
        $subscriber->update(['unsubscribed_at' => now()]);

        return redirect()->route('home')->with('newsletter', 'Vous êtes désinscrit(e) de la lettre d\'information.');
    }
}
