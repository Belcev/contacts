<!doctype html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <title>@yield('title','Kontakty')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
          integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
          crossorigin="anonymous"
          referrerpolicy="no-referrer" />

</head>
<body class="bg-gray-50 text-gray-900">
<div class="max-w-5xl mx-auto p-6">
    <header class="mb-6 flex items-center justify-between">
        <a href="{{ route('contacts.index') }}">
            <h1 class="text-2xl font-semibold">Kontakty</h1>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('contacts.create') }}" class="px-3 py-2 rounded bg-blue-600 text-white">
                <i class="fa-solid fa-user-plus"></i>
            </a>

            <a href="{{ route('contacts.import') }}" class="px-3 py-2 rounded border">
                <i class="fa-solid fa-file-arrow-up"></i>
            </a>

            <form method="post" action="{{ route('contacts.purge') }}"
                  class="inline-block"
                  onsubmit="return confirm('Opravdu smazat VŠECHNY kontakty? Tuto akci nelze vrátit.');">
                @csrf
                <button class="rounded border bg-red-600 text-white px-3 py-2">
                    <i class="fa-solid fa-broom"></i>
                </button>
            </form>
        </div>

    </header>

    @yield('content')
</div>
</body>
</html>
