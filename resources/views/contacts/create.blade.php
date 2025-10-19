@extends('layouts.app')

@section('title', 'Nový kontakt')

@section('content')
    <div class="bg-white rounded shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Vytvořit kontakt</h2>

        <form method="post" action="{{ route('contacts.store') }}" class="space-y-4">
            @include('contacts._form')
        </form>
    </div>
@endsection
