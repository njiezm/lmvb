@foreach (['success' => ['bg-emerald-50 text-emerald-800 ring-emerald-200', 'fa-circle-check'], 'error' => ['bg-red-50 text-red-800 ring-red-200', 'fa-circle-exclamation']] as $key => [$classes, $icon])
    @if (session($key))
        <div data-flash class="mb-6 flex items-start gap-3 rounded-xl px-4 py-3 text-sm ring-1 {{ $classes }}" role="status">
            <i class="fa-solid {{ $icon }} mt-0.5"></i><span class="flex-1">{{ session($key) }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="opacity-60 hover:opacity-100" aria-label="Fermer"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif
@endforeach
@if ($errors->any())
    <div class="mb-6 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-800 ring-1 ring-red-200" role="alert">
        <p class="font-semibold"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Merci de corriger les erreurs ci-dessous :</p>
        <ul class="mt-1 list-inside list-disc">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif
