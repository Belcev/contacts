<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Testing\PendingCommand;
use Illuminate\Support\Str;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

/**
 * @method PendingCommand artisan(string $command, array<string, int|string|true> $parameters = [])
 */
final class ImportContactsCommandTest extends TestCase
{
    public function test_import_contacts_from_xml_batches_and_skips_invalid_and_upserts(): void
    {

        $relative = 'tests/'.Str::uuid()->toString().'-contacts.xml';
        $xml = implode("\n", [
            '<items>',
            '  <item><email>john.doe@example.test</email><first_name>John</first_name><last_name>Doe</last_name></item>',
            '  <item><email>jane.doe@example.test</email><first_name>Jane</first_name><last_name>Doe</last_name></item>',
            '  <item><email></email><first_name>Empty</first_name><last_name>Email</last_name></item>',
            '  <item><email>not-an-email</email><first_name>Bad</first_name><last_name>Email</last_name></item>',
            '  <item><email>alpha@example.test</email><first_name>Alpha</first_name><last_name>One</last_name></item>',
            '  <item><email>bravo@example.test</email><first_name>Bravo</first_name><last_name>Two</last_name></item>',
            '  <item><email>charlie@example.test</email><first_name>Charlie</first_name><last_name>Three</last_name></item>',
            '</items>',
        ]);

        Storage::disk('local')->put($relative, $xml);

        // 2) Získej ABSOLUTNÍ cestu, tu dáš importeru
        $path = Storage::disk('local')->path($relative);

        $this->artisan('contacts:import', [
            'path'    => $path
        ])->assertExitCode(0);

        // …asserty nad DB (nechávám beze změny)
        $this->assertDatabaseHas('contacts', ['email' => 'john.doe@example.test', 'first_name' => 'John', 'last_name' => 'Doe']);
        $this->assertDatabaseHas('contacts', ['email' => 'jane.doe@example.test', 'first_name' => 'Jane', 'last_name' => 'Doe']);
        $this->assertDatabaseHas('contacts', ['email' => 'alpha@example.test', 'first_name' => 'Alpha', 'last_name' => 'One']);
        $this->assertDatabaseHas('contacts', ['email' => 'bravo@example.test', 'first_name' => 'Bravo', 'last_name' => 'Two']);
        $this->assertDatabaseHas('contacts', ['email' => 'charlie@example.test', 'first_name' => 'Charlie', 'last_name' => 'Three']);
        $this->assertDatabaseMissing('contacts', ['email' => 'not-an-email']);
        $this->assertDatabaseMissing('contacts', ['email' => '']);

        // UPDATE importu – přepiš soubor a zavolej znovu
        $xml2 = implode("\n", [
            '<items>',
            '  <item><email>john.doe@example.test</email><first_name>Johnny</first_name><last_name>Doe</last_name></item>',
            '  <item><email>alpha@example.test</email><first_name>Alpha</first_name><last_name>Prime</last_name></item>',
            '</items>',
        ]);
        Storage::disk('local')->put($relative, $xml2);
        $path = Storage::disk('local')->path($relative);

        $this->artisan('contacts:import', [
            'path'    => $path,
            '--batch' => 1,
        ])->assertExitCode(0);

        $this->assertDatabaseHas('contacts', ['email' => 'john.doe@example.test', 'first_name' => 'Johnny', 'last_name' => 'Doe']);
        $this->assertDatabaseHas('contacts', ['email' => 'alpha@example.test', 'first_name' => 'Alpha', 'last_name' => 'Prime']);
    }

    public function test_import_contacts_with_delete_option_removes_file(): void
    {
        $relative = 'tests/'.Str::uuid()->toString().'-contacts.xml';
        $xml = implode("\n", [
            '<items>',
            '  <item><email>delete.me@example.test</email><first_name>Del</first_name><last_name>Ete</last_name></item>',
            '</items>',
        ]);

        Storage::disk('local')->put($relative, $xml);
        $path = Storage::disk('local')->path($relative);
        $this->assertFileExists($path);

        $this->artisan('contacts:import', [
            'path'    => $path,
            '--delete' => true,
            '--batch' => 10,
        ])->assertExitCode(0);

        // ověř, že importer opravdu smazal
        $this->assertFalse(Storage::disk('local')->exists($relative));
        $this->assertDatabaseHas('contacts', ['email' => 'delete.me@example.test', 'first_name' => 'Del', 'last_name' => 'Ete']);
    }
}
