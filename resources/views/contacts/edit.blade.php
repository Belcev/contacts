@extends('layouts.app')

@section('title', 'Upravit kontakt')

@section('content')
    <div class="bg-white rounded shadow p-6">
        <h2 class="text-xl font-semibold mb-4">
            <i class="fa-solid fa-user-pen"></i> {{ $contact->first_name }} {{ $contact->last_name }}
        </h2>

        <form method="post" action="{{ route('contacts.update', $contact) }}" class="space-y-4">
            @method('PUT')
            @include('contacts._form', ['contact' => $contact])
        </form>
    </div>
@endsection
