<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Jobs\ImportContactsJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImportController extends Controller
{
    public function create(): Factory|View
    {
        return view('contacts.import');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => ['required','file','mimetypes:application/xml,text/xml','max:512000']
        ]);

        $uploadedFile = $validated['file'];
        $relative = $uploadedFile->store('private/imports', 'local');

        $disk = Storage::disk('local');
        $absolute = $disk->path($relative);

        if (!$disk->exists($relative) || !is_readable($absolute)) {
            return back()->withErrors(['file' => 'Soubor se nepodařilo uložit nebo jej nelze číst.']);
        }

        ImportContactsJob::dispatch($absolute);

        return back()->with('ok', 'Soubor nahrán. Import běží na pozadí.');
    }
}
