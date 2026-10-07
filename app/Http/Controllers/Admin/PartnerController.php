<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Services\ImageUploader;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function __construct(private ImageUploader $images) {}

    public function index()
    {
        return view('admin.partners.index', ['partners' => Partner::orderBy('sort_order')->orderBy('name')->get()]);
    }

    public function create()
    {
        return view('admin.partners.form', ['partner' => new Partner(['active' => true, 'sort_order' => 100])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['logo'] = $request->hasFile('logo') ? $this->images->store($request->file('logo'), 'partners', 600) : null;
        Partner::create($data);

        return redirect()->route('admin.partners.index')->with('success', 'Partenaire ajouté.');
    }

    public function edit(Partner $partner)
    {
        return view('admin.partners.form', compact('partner'));
    }

    public function update(Request $request, Partner $partner)
    {
        $data = $this->validated($request);
        $data['logo'] = $this->images->replace($request->file('logo'), $partner->logo, 'partners', 600);
        $partner->update($data);

        return redirect()->route('admin.partners.index')->with('success', 'Partenaire mis à jour.');
    }

    public function destroy(Partner $partner)
    {
        $this->images->delete($partner->logo);
        $partner->delete();

        return back()->with('success', 'Partenaire supprimé.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'url' => 'nullable|url|max:255',
            'sort_order' => 'required|integer|min:0|max:999',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
        ]);
        $data['active'] = $request->boolean('active');
        unset($data['logo']);

        return $data;
    }
}
