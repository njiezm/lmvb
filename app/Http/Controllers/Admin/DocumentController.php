<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    public function index()
    {
        return view('admin.documents.index', ['documents' => Document::orderBy('category')->orderBy('title')->get()]);
    }

    public function create()
    {
        return view('admin.documents.form', ['document' => new Document(['active' => true, 'category' => 'general'])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['file'] = $request->hasFile('file') ? $this->storeFile($request) : null;
        Document::create($data);

        return redirect()->route('admin.documents.index')->with('success', 'Document ajouté.');
    }

    public function edit(Document $document)
    {
        return view('admin.documents.form', compact('document'));
    }

    public function update(Request $request, Document $document)
    {
        $data = $this->validated($request, $document);
        if ($request->hasFile('file')) {
            $this->deleteFile($document->file);
            $data['file'] = $this->storeFile($request);
        }
        $document->update($data);

        return redirect()->route('admin.documents.index')->with('success', 'Document mis à jour.');
    }

    public function destroy(Document $document)
    {
        $this->deleteFile($document->file);
        $document->delete();

        return back()->with('success', 'Document supprimé.');
    }

    private function validated(Request $request, ?Document $document = null): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'category' => ['required', Rule::in(array_keys(Document::CATEGORIES))],
            'description' => 'nullable|string|max:1000',
            'url' => [Rule::requiredIf(! $request->hasFile('file') && ! $document?->file), 'nullable', 'url', 'max:255'],
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,odt,ods,jpg,png|max:20480',
        ]);
        $data['active'] = $request->boolean('active');
        unset($data['file']);

        return $data;
    }

    private function storeFile(Request $request): string
    {
        $file = $request->file('file');
        $dir = public_path('uploads/documents');
        File::ensureDirectoryExists($dir);
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'-'.Str::random(6).'.'.$file->extension();
        $file->move($dir, $name);

        return 'uploads/documents/'.$name;
    }

    private function deleteFile(?string $path): void
    {
        if ($path && str_starts_with($path, 'uploads/documents/') && ! str_contains($path, '..')) {
            File::delete(public_path($path));
        }
    }
}
