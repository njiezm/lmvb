@props(['action', 'confirm' => 'Supprimer définitivement cet élément ?', 'label' => null])
<form action="{{ $action }}" method="POST" class="inline" onsubmit="return confirm(@js($confirm))">
    @csrf
    @method('DELETE')
    <button type="submit" {{ $attributes->merge(['class' => 'inline-flex h-9 items-center justify-center gap-1.5 rounded-lg px-2.5 text-sm text-red-600 hover:bg-red-50']) }} title="Supprimer" aria-label="Supprimer">
        <i class="fa-regular fa-trash-can"></i>@if ($label)<span>{{ $label }}</span>@endif
    </button>
</form>
