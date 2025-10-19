<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_displays_form(): void
    {
        $this->get(route('contacts.create'))
            ->assertOk();
    }

    public function test_store_creates_contact_and_redirects_with_flash(): void
    {
        $payload = [
            'first_name' => 'Alice',
            'last_name'  => 'Zephyr',
            'email'      => 'alice.zephyr@example.test',
        ];

        $resp = $this->post(route('contacts.store'), $payload);

        $resp->assertRedirect(route('contacts.index'))
            ->assertSessionHas('ok', 'Kontakt vytvořen.');

        $this->assertDatabaseHas('contacts', $payload);
    }

    public function test_show_displays_contact(): void
    {
        $contact = Contact::factory()->create([
            'first_name' => 'Bob',
            'last_name'  => 'Yellow',
            'email'      => 'bob.yellow@example.test',
        ]);

        $this->get(route('contacts.show', $contact))
            ->assertOk()
            ->assertSee('bob.yellow@example.test')
            ->assertSee('Bob')
            ->assertSee('Yellow');
    }

    public function test_edit_displays_form(): void
    {
        $contact = Contact::factory()->create();

        $this->get(route('contacts.edit', $contact))
            ->assertOk();
    }

    public function test_update_changes_contact_and_redirects_with_flash(): void
    {
        $contact = Contact::factory()->create([
            'first_name' => 'Carol',
            'last_name'  => 'Xavier',
            'email'      => 'carol.x@example.test',
        ]);

        $payload = [
            'first_name' => 'Caroline',
            'last_name'  => 'Xavier',
            'email'      => 'caroline.x@example.test',
        ];

        $resp = $this->put(route('contacts.update', $contact), $payload);

        $resp->assertRedirect(route('contacts.index'))
            ->assertSessionHas('ok', 'Kontakt upraven.');

        $this->assertDatabaseHas('contacts', $payload);
        $this->assertDatabaseMissing('contacts', ['email' => 'carol.x@example.test']);
    }

    public function test_update_validates_unique_email_ignoring_current(): void
    {
        $a = Contact::factory()->create(['email' => 'a@test.cz']);
        $b = Contact::factory()->create(['email' => 'b@test.cz']);

        $resp = $this->put(route('contacts.update', $b), [
            'first_name' => $b->first_name,
            'last_name'  => $b->last_name,
            'email'      => 'a@test.cz',
        ]);

        $resp->assertSessionHasErrors('email');

        $resp2 = $this->put(route('contacts.update', $a), [
            'first_name' => $a->first_name,
            'last_name'  => $a->last_name,
            'email'      => 'a@test.cz',
        ]);
        $resp2->assertRedirect(route('contacts.index'))
            ->assertSessionHas('ok', 'Kontakt upraven.');
    }

    public function test_destroy_deletes_contact_and_redirects_with_flash(): void
    {
        $contact = Contact::factory()->create();

        $resp = $this->delete(route('contacts.destroy', $contact));

        $resp->assertRedirect(route('contacts.index'))
            ->assertSessionHas('ok', 'Kontakt smazán.');

        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }

    public function test_purge_truncates_contacts_and_redirects_with_flash(): void
    {
        Contact::factory()->count(3)->create();

        $resp = $this->post(route('contacts.purge'));

        $resp->assertRedirect(route('contacts.index'))
            ->assertSessionHas('ok', 'Všechny kontakty byly smazány.');

        $this->assertDatabaseCount('contacts', 0);
    }
}
