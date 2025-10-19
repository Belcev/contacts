<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Factory|View
    {

        /** @var string|null $q */
        $q = request('q');
        $contacts = Contact::query()
            ->search($q)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(25)
            ->withQueryString();

        return view('contacts.index', ['contacts' => $contacts, 'q' => $q]);
    }

    public function create(): Factory|View
    {
        return view('contacts.create');
    }

    public function store(StoreContactRequest $request): RedirectResponse
    {
        Contact::create($request->validated());

        return redirect()
            ->route('contacts.index')
            ->with('ok', 'Kontakt vytvořen.');
    }

    public function show(Contact $contact): Factory|View
    {
        return view('contacts.show', ['contact' => $contact]);
    }

    public function edit(Contact $contact): Factory|View
    {
        return view('contacts.edit', ['contact' => $contact]);
    }

    public function update(UpdateContactRequest $request, Contact $contact): RedirectResponse
    {
        $contact->update($request->validated());
        return redirect()
            ->route('contacts.index')
            ->with('ok', 'Kontakt upraven.');
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $contact->delete();
        return redirect()
            ->route('contacts.index')
            ->with('ok', 'Kontakt smazán.');
    }

    public function purge(): RedirectResponse
    {
        Schema::disableForeignKeyConstraints();
        Contact::truncate();
        Schema::enableForeignKeyConstraints();

        return redirect()
            ->route('contacts.index')
            ->with('ok', 'Všechny kontakty byly smazány.');
    }
}
