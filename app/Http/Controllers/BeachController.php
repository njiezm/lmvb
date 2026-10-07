<?php

namespace App\Http\Controllers;

use App\Models\BeachEvent;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BeachController extends Controller
{
    public function index()
    {
        $upcomingEvents = BeachEvent::upcoming()->orderBy('start_date')->get();
        $finishedEvents = BeachEvent::finished()->orderByDesc('start_date')->take(6)->get();
        $videos = Gallery::active()->where('category', 'beach')->where('type', 'video')->latest()->take(4)->get();
        $photos = Gallery::active()->where('category', 'beach')->where('type', 'image')->latest()->take(8)->get();

        return view('beach.index', compact('upcomingEvents', 'finishedEvents', 'videos', 'photos'));
    }

    public function calendar()
    {
        $events = BeachEvent::where('status', '!=', 'cancelled')->orderBy('start_date')->get()
            ->groupBy(fn ($e) => $e->start_date->translatedFormat('F Y'));

        return view('beach.calendar', compact('events'));
    }

    public function show(BeachEvent $event)
    {
        return view('beach.show', compact('event'));
    }

    public function register(Request $request, BeachEvent $event)
    {
        if (! $event->canRegister()) {
            return back()->with('error', 'Les inscriptions sont fermées ou le tournoi est complet.');
        }

        $data = $request->validate([
            'team_name' => ['required', 'string', 'max:100', Rule::unique('beach_registrations')->where('beach_event_id', $event->id)],
            'player1_name' => 'required|string|max:100',
            'player1_email' => 'required|email|max:150',
            'player2_name' => 'required|string|max:100',
            'player2_email' => 'required|email|max:150|different:player1_email',
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9 +().-]{8,20}$/'],
            'website' => 'prohibited', // pot de miel anti-robots
        ], [
            'team_name.unique' => 'Une équipe porte déjà ce nom pour ce tournoi.',
        ]);
        unset($data['website']);

        DB::transaction(function () use ($event, $data) {
            $locked = BeachEvent::whereKey($event->id)->lockForUpdate()->first();
            if ($locked->max_teams && $locked->registered_teams >= $locked->max_teams) {
                abort(back()->with('error', 'Ce tournoi est complet.'));
            }
            $locked->registrations()->create($data);
            $locked->increment('registered_teams');
        });

        return back()->with('success', 'Inscription enregistrée ! Vous recevrez une confirmation de la ligue.');
    }
}
