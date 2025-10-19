@extends('layouts.app')
@section('title','Import kontaktů')

@section('content')
    <div class="bg-white rounded shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Import XML</h2>

        @if(session('ok'))
            <div class="mb-4 rounded bg-green-100 text-green-800 px-4 py-2">{{ session('ok') }}</div>
        @endif

        <form method="post" action="{{ route('contacts.import.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm mb-1">XML soubor</label>
                <input type="file" name="file" accept=".xml" class="block w-full">
                @error('file')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>

            <div class="pt-2">
                <a href="{{ route('contacts.index') }}"
                   class="inline-block px-4 py-2 bg-neutral-600 text-white rounded">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>

                <button class="ml-3 px-4 py-2 bg-blue-600 text-white rounded">
                    <i class="fa-solid fa-file-arrow-up"></i>
                </button>
            </div>
        </form>
    </div>
@endsection
