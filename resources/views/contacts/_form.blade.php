@csrf

<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm mb-1">Jméno</label>
        <input name="first_name" value="{{ old('first_name', $contact->first_name ?? '') }}"
               class="border rounded w-full px-3 py-2">
        @error('first_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>

    <div>
        <label class="block text-sm mb-1">Příjmení</label>
        <input name="last_name" value="{{ old('last_name', $contact->last_name ?? '') }}"
               class="border rounded w-full px-3 py-2">
        @error('last_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm mb-1">E-mail</label>
        <input name="email" type="email" value="{{ old('email', $contact->email ?? '') }}"
               class="border rounded w-full px-3 py-2">
        @error('email')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
</div>

<div class="pt-2">
    <a href="{{ route('contacts.index') }}"
       class="inline-block px-4 py-2 bg-neutral-600 text-white rounded">
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <button class="ml-3 px-4 py-2 bg-blue-600 text-white rounded"><i class="fa-solid fa-floppy-disk"></i></button>
</div>
