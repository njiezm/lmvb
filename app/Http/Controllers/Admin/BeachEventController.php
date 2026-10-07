<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BeachEvent;
use App\Models\BeachRegistration;
use App\Services\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BeachEventController extends Controller
{
    public function __construct(private ImageUploader $images) {}

    public function index()
    {
        $events = BeachEvent::withCount('registrations')->orderByDesc('start_date')->paginate(20);

        return view('admin.beach.index', compact('events'));
    }

    public function create()
    {
        return view('admin.beach.form', ['event' => new BeachEvent([
            'type' => 'tournament', 'status' => 'upcoming', 'registration_open' => true,
        ])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['image'] = $request->hasFile('image') ? $this->images->store($request->file('image'), 'beach') : null;

        BeachEvent::create($data);

        return redirect()->route('admin.beach.index')->with('success', 'Événement beach créé.');
    }

    public function edit(BeachEvent $event)
    {
        return view('admin.beach.form', compact('event'));
    }

    public function update(Request $request, BeachEvent $event)
    {
        $data = $this->validated($request);
        if ($data['title'] !== $event->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $event->id);
        }
        $data['image'] = $this->images->replace($request->file('image'), $event->image, 'beach');

        $event->update($data);

        return redirect()->route('admin.beach.index')->with('success', 'Événement mis à jour.');
    }

    public function destroy(BeachEvent $event)
    {
        $this->images->delete($event->image);
        $event->delete();

        return back()->with('success', 'Événement supprimé.');
    }

    public function registrations(BeachEvent $event)
    {
        $event->load(['registrations' => fn ($q) => $q->latest()]);

        return view('admin.beach.registrations', compact('event'));
    }

    public function updateRegistration(Request $request, BeachRegistration $registration)
    {
        $data = $request->validate(['status' => ['required', Rule::in(array_keys(BeachRegistration::STATUSES))]]);

        $event = $registration->event;
        $wasActive = $registration->status !== 'cancelled';
        $registration->update($data);
        $isActive = $registration->status !== 'cancelled';
        if ($wasActive !== $isActive) {
            $isActive ? $event->increment('registered_teams') : $event->decrement('registered_teams');
        }

        return back()->with('success', 'Inscription mise à jour.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'required|string|max:20000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:6144',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string|max:200',
            'type' => ['required', Rule::in(array_keys(BeachEvent::TYPES))],
            'category' => 'nullable|string|max:20',
            'status' => ['required', Rule::in(array_keys(BeachEvent::STATUSES))],
            'max_teams' => 'nullable|integer|min:2|max:256',
            'prize_pool' => 'nullable|numeric|min:0|max:100000',
            'contact_email' => 'nullable|email|max:150',
        ]);
        $data['description'] = safe_html($data['description']);
        $data['registration_open'] = $request->boolean('registration_open');
        unset($data['image']);

        return $data;
    }

    private function uniqueSlug(string $title, ?int $ignore = null): string
    {
        $base = Str::slug($title) ?: 'beach';
        $slug = $base;
        $i = 2;
        while (BeachEvent::where('slug', $slug)->when($ignore, fn ($q) => $q->where('id', '!=', $ignore))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
