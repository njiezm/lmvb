@props(['name', 'label', 'current' => null, 'removable' => null, 'help' => 'JPG, PNG ou WebP. L\'image est automatiquement redimensionnée et optimisée.', 'multiple' => false])
<div {{ $attributes->only('class') }}>
    <span class="block text-sm font-medium text-slate-700">{{ $label }}</span>
    <div class="mt-1 flex items-center gap-4">
        <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200" data-preview="{{ $name }}">
            @if ($current)
                <img src="{{ asset($current) }}" alt="" class="h-full w-full object-cover">
            @else
                <i class="fa-regular fa-image text-2xl text-slate-300"></i>
            @endif
        </div>
        <div class="min-w-0 flex-1">
            <input type="file" name="{{ $name }}{{ $multiple ? '[]' : '' }}" accept="image/*" @if ($multiple) multiple @endif
                   class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-full file:border-0 file:bg-navy-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-navy-700 hover:file:bg-navy-100"
                   onchange="var p=document.querySelector('[data-preview=&quot;{{ $name }}&quot;]');if(this.files[0]){p.innerHTML='<img class=&quot;h-full w-full object-cover&quot; src=&quot;'+URL.createObjectURL(this.files[0])+'&quot;>'}">
            @if ($help)<p class="mt-1 text-xs text-slate-500">{{ $help }}</p>@endif
            @if ($current && $removable)
                <label class="mt-2 inline-flex items-center gap-2 text-xs text-red-600"><input type="checkbox" name="{{ $removable }}" value="1" class="rounded border-slate-300 text-red-600"> Supprimer l'image actuelle</label>
            @endif
        </div>
    </div>
    @error($name)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    @error($name.'.*')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
