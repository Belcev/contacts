<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ContactFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_shows_contacts_and_allows_search(): void
    {
        Contact::factory()->create([
            'first_name' => 'Jan',
            'last_name'  => 'Novák',
            'email'      => 'jan.novak@example.test',
        ]);

        Contact::factory()->count(3)->create();

        $resp = $this->get('/contacts?q=novak');
        $resp->assertStatus(200)
            ->assertSee('jan.novak@example.test');
    }

    public function test_store_validates_unique_email(): void
    {
        Contact::factory()->create(['email' => 'dup@test.cz']);

        $resp = $this->post('/contacts', [
            'first_name' => 'Pepa',
            'last_name'  => 'Test',
            'email'      => 'dup@test.cz',
        ]);

        $resp->assertSessionHasErrors('email');
    }

}
