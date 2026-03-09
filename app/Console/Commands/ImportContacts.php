<?php

declare(strict_types=1);

namespace App\Console\Commands;

use DOMElement;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use XMLReader;

final class ImportContacts extends Command
{
    protected $signature = 'contacts:import
        {path : Path to the XML file}
        {--batch=2000 : Batch size for upsert}
        {--delete : Delete the file after a successful import}
    ';

    protected $description = 'Stream-import contacts from an XML file';

    public function handle(): int
    {
        $path = (string) $this->argument('path');
        $batchSize = (int) $this->option('batch');
        $deleteAfter = (bool) $this->option('delete');

        if (!is_readable($path)) {
            $this->error("File is not readable: {$path}");
            return self::FAILURE;
        }

        DB::disableQueryLog();

        $reader = new XMLReader();
        $reader->open($path, null, LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_COMPACT);

        $batch = [];
        $now = Carbon::now();
        $imported = 0;

        $this->info("Importing: {$path}");
        $this->output->progressStart();

        while ($reader->read()) {
            if ($reader->nodeType !== XMLReader::ELEMENT) {
                continue;
            }
            if ($reader->name !== 'item') {
                continue;
            }
            $node = $reader->expand();
            if (!$node instanceof DOMElement) {
                continue;
            }

            $email = trim($node->getElementsByTagName('email')->item(0)->textContent ?? '');
            if ($email === '') {
                continue;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $batch[] = [
                'email'      => $email,
                'first_name' => trim($node->getElementsByTagName('first_name')->item(0)->textContent ?? ''),
                'last_name'  => trim($node->getElementsByTagName('last_name')->item(0)->textContent ?? ''),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($batch) >= $batchSize) {
                $this->upsert($batch);
                $imported += count($batch);
                $this->output->progressAdvance(count($batch));
                $batch = [];
            }
        }

        if ($batch !== []) {
            $this->upsert($batch);
            $imported += count($batch);
            $this->output->progressAdvance(count($batch));
        }

        $reader->close();
        $this->output->progressFinish();

        $this->info("Done. Imported: {$imported}");

        if ($deleteAfter) {
            if (is_file($path) && @unlink($path)) {
                $this->info("File deleted: {$path}");
            } else {
                $this->warn("Could not delete file: {$path}");
            }
        }

        return self::SUCCESS;
    }

    /**
     * @param list<array<string, Carbon|string>> $batch
     */
    private function upsert(array $batch): void
    {
        DB::table('contacts')->upsert(
            $batch,
            ['email'],
            ['first_name', 'last_name', 'updated_at'],
        );
    }
}
