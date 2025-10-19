<?php

declare(strict_types=1);

namespace App\Console\Commands;

use SimpleXMLElement;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use XMLReader;

final class ImportContacts extends Command
{
    public const int DEFAULT_BATCH_SIZE = 10000;

    protected $signature = 'contacts:import
        {path : Cesta k XML souboru}
        {--batch=2000 : Velikost dávky pro upsert}
        {--delete : Po úspěšném importu soubor smazat}
        ';

    protected $description = 'Streaming import kontaktů z XML';

    public function handle(): int
    {
        $path = (string) $this->argument('path');
        $batchSize = (int) ($this->option('batch') ?? self::DEFAULT_BATCH_SIZE);
        $deleteAfter = (bool) $this->option('delete');

        if (!is_readable($path)) {
            $this->error("Soubor nelze číst: {$path}");
            return self::FAILURE;
        }

        DB::disableQueryLog();

        $reader = new XMLReader();
        $reader->open($path, null, LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_COMPACT);

        $rows = [];
        $now  = Carbon::now();
        $done = 0;

        $this->info("Start importu: {$path}");
        $this->output->progressStart();

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::ELEMENT && $reader->name === 'item') {

                $xml = new SimpleXMLElement($reader->readOuterXML());

                $email = trim((string) ($xml->email ?? ''));
                $first = trim((string) ($xml->first_name ?? ''));
                $last  = trim((string) ($xml->last_name ?? ''));
                if ($email === '') {
                    continue;
                }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }

                $rows[] = [
                    'email'      => $email,
                    'first_name' => $first,
                    'last_name'  => $last,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($rows) >= $batchSize) {
                    $this->flush($rows);
                    $done += count($rows);
                    $rows = [];
                    $this->output->progressAdvance($batchSize);
                }
            }
        }

        if ($rows !== []) {
            $this->flush($rows);
            $done += count($rows);
            $this->output->progressAdvance(count($rows));
        }

        $reader->close();
        $this->output->progressFinish();

        $this->info("Hotovo. Zpracováno: {$done} ( dávka {$batchSize})");

        if ($deleteAfter) {
            if (is_file($path) && @unlink($path)) {
                $this->info("Soubor {$path} byl úspěšně smazán (--delete).");
            } else {
                $this->warn("Soubor {$path} se nepodařilo smazat nebo neexistuje.");
            }
        }

        return self::SUCCESS;
    }

    /**
     * @param list<array<string, Carbon|string>> $rows
     */
    private function flush(array $rows): void
    {

        DB::table('contacts')->upsert(
            $rows,
            ['email'],
            ['first_name','last_name','updated_at']
        );
    }
}
