<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $contacts = Contact::visibleTo($request->user())
            ->with('club')
            ->when($request->query('status') !== null && $request->query('status') !== '', fn ($q) => $q->where('read', $request->boolean('status')))
            ->when($request->query('q'), fn ($q, $s) => $q->where(fn ($q) => $q->whereLike('name', "%$s%")->orWhereLike('subject', "%$s%")->orWhereLike('email', "%$s%")))
            ->latest()
            ->paginate(20)->withQueryString();

        return view('admin.contacts.index', compact('contacts'));
    }

    public function show(Request $request, Contact $contact)
    {
        $this->authorizeContact($request, $contact);
        if (! $contact->read) {
            $contact->update(['read' => true]);
        }

        return view('admin.contacts.show', compact('contact'));
    }

    public function destroy(Request $request, Contact $contact)
    {
        $this->authorizeContact($request, $contact);
        $contact->delete();

        return redirect()->route('admin.contacts.index')->with('success', 'Message supprimé.');
    }

    /** Marque comme lus les messages cochés (ou tous si aucun n'est coché). */
    public function markRead(Request $request)
    {
        $data = $request->validate(['ids' => 'nullable|array', 'ids.*' => 'integer']);

        $count = Contact::visibleTo($request->user())
            ->when(! empty($data['ids']), fn ($q) => $q->whereIn('id', $data['ids']))
            ->where('read', false)
            ->update(['read' => true]);

        return back()->with('success', "$count message(s) marqué(s) comme lu(s).");
    }

    public function destroyRead(Request $request)
    {
        $count = Contact::visibleTo($request->user())->where('read', true)->delete();

        return back()->with('success', "$count message(s) lu(s) supprimé(s).");
    }

    private function authorizeContact(Request $request, Contact $contact): void
    {
        abort_unless($request->user()->isSuperAdmin() || $contact->club_id === $request->user()->club_id, 403);
    }
}
