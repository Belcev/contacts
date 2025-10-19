@extends('layouts.app')
@section('title', 'Detail kontaktu')

@section('content')
    <div class="bg-white rounded shadow p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold">
                    {{ $contact->first_name }} {{ $contact->last_name }}
                </h2>
                <p class="text-gray-600">
                    <i class="fa-solid fa-envelope mr-1"></i> {{ $contact->email }}
                </p>
                <div class="mt-3 text-sm text-gray-500">
                    <div>Vytvořeno: {{ $contact->created_at?->format('d.m.Y H:i') }}</div>
                    <div>Naposledy upraveno: {{ $contact->updated_at?->format('d.m.Y H:i') }}</div>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('contacts.edit', $contact) }}"
                   class="px-3 py-2 rounded bg-gray-800 text-white hover:bg-gray-900">
                    <i class="fa-solid fa-user-pen"></i>
                </a>

                <form action="{{ route('contacts.destroy', $contact) }}" method="post"
                      onsubmit="return confirm('Opravdu smazat tento kontakt?');">
                    @csrf
                    @method('delete')
                    <button class="px-3 py-2 rounded bg-red-600 text-white hover:bg-red-700">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('contacts.index') }}"
               class="inline-block px-4 py-2 bg-neutral-600 text-white rounded">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
    </div>
@endsection
