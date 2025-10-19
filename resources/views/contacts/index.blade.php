@extends('layouts.app')
@section('title','Seznam kontaktů')
@section('content')
    <form method="get" class="mb-4 flex gap-2">
        <input type="text" name="q" value="{{ $q }}" placeholder="Hledat jméno, příjmení, e-mail"
               class="border rounded px-3 py-2 w-full">
        <button class="px-3 py-2 rounded bg-gray-800 text-white"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full">
            <thead class="bg-gray-100">
            <tr>
                <th class="text-left p-3">Jméno</th>
                <th class="text-left p-3">Příjmení</th>
                <th class="text-left p-3">E-mail</th>
                <th class="p-3">Akce</th>
            </tr>
            </thead>
            <tbody>
            @forelse($contacts as $c)
                <tr class="border-t">
                    <td class="p-3">{{ $c->first_name }}</td>
                    <td class="p-3">{{ $c->last_name }}</td>
                    <td class="p-3">{{ $c->email }}</td>
                    <td class="p-3 text-right">
                        <span class="flex items-center justify-end gap-2">
                            <a href="{{ route('contacts.show', $c) }}">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </a>
                            <a href="{{ route('contacts.edit',$c) }}">
                                <i class="fa-solid fa-user-pen"></i>
                            </a>
                            <form action="{{ route('contacts.destroy',$c) }}" method="post" onsubmit="return confirm('Smazat?')">
                                @csrf @method('delete')
                                <button class="text-red-600">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td class="p-3" colspan="4">Žádné kontakty</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $contacts->links() }}</div>
@endsection
