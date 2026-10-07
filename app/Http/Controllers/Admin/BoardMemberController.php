<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoardMember;
use App\Services\ImageUploader;
use Illuminate\Http\Request;

/** Comité directeur de la ligue. */
class BoardMemberController extends Controller
{
    public function __construct(private ImageUploader $images) {}

    public function index()
    {
        return view('admin.board.index', ['members' => BoardMember::orderBy('sort_order')->orderBy('name')->get()]);
    }

    public function create()
    {
        return view('admin.board.form', ['member' => new BoardMember(['active' => true, 'sort_order' => 100])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['photo'] = $request->hasFile('photo') ? $this->images->store($request->file('photo'), 'board', 600) : null;
        BoardMember::create($data);

        return redirect()->route('admin.board.index')->with('success', 'Membre ajouté.');
    }

    public function edit(BoardMember $member)
    {
        return view('admin.board.form', compact('member'));
    }

    public function update(Request $request, BoardMember $member)
    {
        $data = $this->validated($request);
        $data['photo'] = $this->images->replace($request->file('photo'), $member->photo, 'board', 600);
        $member->update($data);

        return redirect()->route('admin.board.index')->with('success', 'Membre mis à jour.');
    }

    public function destroy(BoardMember $member)
    {
        $this->images->delete($member->photo);
        $member->delete();

        return back()->with('success', 'Membre retiré.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'role' => 'required|string|max:120',
            'bio' => 'nullable|string|max:3000',
            'sort_order' => 'required|integer|min:0|max:999',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);
        $data['active'] = $request->boolean('active');
        unset($data['photo']);

        return $data;
    }
}
