<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Str;
use Illuminate\Testing\PendingCommand;
use Tests\TestCase;

final class ImportContactsCommandTest extends TestCase
{
    public function test_import_contacts_from_xml_batches_and_skips_invalid_and_upserts(): void
    {
        $path = base_path('tmp/'.Str::uuid()->toString().'-contacts.xml');

        $xml = [];
        $xml[] = '<items>';
        $xml[] = '  <item><email>john.doe@example.test</email><first_name>John</first_name><last_name>Doe</last_name></item>';
        $xml[] = '  <item><email>jane.doe@example.test</email><first_name>Jane</first_name><last_name>Doe</last_name></item>';
        $xml[] = '  <item><email></email><first_name>Empty</first_name><last_name>Email</last_name></item>';
        $xml[] = '  <item><email>not-an-email</email><first_name>Bad</first_name><last_name>Email</last_name></item>';
        $xml[] = '  <item><email>alpha@example.test</email><first_name>Alpha</first_name><last_name>One</last_name></item>';
        $xml[] = '  <item><email>bravo@example.test</email><first_name>Bravo</first_name><last_name>Two</last_name></item>';
        $xml[] = '  <item><email>charlie@example.test</email><first_name>Charlie</first_name><last_name>Three</last_name></item>';
        $xml[] = '</items>';

        file_put_contents($path, implode("\n", $xml));

        /** @var PendingCommand $import */
        $import = $this->artisan('contacts:import', [
            'path' => $path,
            '--batch' => 2,
        ]);
        $import->assertExitCode(0);

        $this->assertDatabaseHas('contacts', [
            'email' => 'john.doe@example.test', 'first_name' => 'John', 'last_name' => 'Doe',
        ]);
        $this->assertDatabaseHas('contacts', [
            'email' => 'jane.doe@example.test', 'first_name' => 'Jane', 'last_name' => 'Doe',
        ]);
        $this->assertDatabaseHas('contacts', [
            'email' => 'alpha@example.test', 'first_name' => 'Alpha', 'last_name' => 'One',
        ]);
        $this->assertDatabaseHas('contacts', [
            'email' => 'bravo@example.test', 'first_name' => 'Bravo', 'last_name' => 'Two',
        ]);
        $this->assertDatabaseHas('contacts', [
            'email' => 'charlie@example.test', 'first_name' => 'Charlie', 'last_name' => 'Three',
        ]);
        $this->assertDatabaseMissing('contacts', ['email' => 'not-an-email']);
        $this->assertDatabaseMissing('contacts', ['email' => '']);

        $xml2 = [];
        $xml2[] = '<items>';
        $xml2[] = '  <item><email>john.doe@example.test</email><first_name>Johnny</first_name><last_name>Doe</last_name></item>';
        $xml2[] = '  <item><email>alpha@example.test</email><first_name>Alpha</first_name><last_name>Prime</last_name></item>';
        $xml2[] = '</items>';
        file_put_contents($path, implode("\n", $xml2));

        /** @var PendingCommand $import */
        $import = $this->artisan('contacts:import', [
            'path' => $path,
            '--batch' => 1,
        ]);
        $import->assertExitCode(0);

        $this->assertDatabaseHas('contacts', [
            'email' => 'john.doe@example.test', 'first_name' => 'Johnny', 'last_name' => 'Doe',
        ]);
        $this->assertDatabaseHas('contacts', [
            'email' => 'alpha@example.test', 'first_name' => 'Alpha', 'last_name' => 'Prime',
        ]);
    }

    public function test_import_contacts_with_delete_option_removes_file(): void
    {
        $path = base_path('tmp/'.Str::uuid()->toString().'-contacts.xml');

        $xml = [];
        $xml[] = '<items>';
        $xml[] = '  <item><email>delete.me@example.test</email><first_name>Del</first_name><last_name>Ete</last_name></item>';
        $xml[] = '</items>';
        file_put_contents($path, implode("\n", $xml));
        $this->assertFileExists($path);


        /** @var PendingCommand $import */
        $import = $this->artisan('contacts:import', [
            'path' => $path,
            '--delete' => true,
            '--batch' => 10,
        ]);
        $import->assertExitCode(0);

        $this->assertFileDoesNotExist($path);
        $this->assertDatabaseHas('contacts', [
            'email' => 'delete.me@example.test', 'first_name' => 'Del', 'last_name' => 'Ete',
        ]);
    }
}
