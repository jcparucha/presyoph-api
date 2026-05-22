<?php

namespace App\Traits\Seeders;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

trait CanBatchSeedFromJson
{
    /**
     * Get the data from the file `$path` and use it to seed the `$table`.
     *
     * It is using `DB::upsert` under the hood to insert the data or update the existing one.
     *
     * @param  string  $path  - file path
     * @param  string  $table  - table to seed
     * @return void
     */
    public function seedFromFile(string $path, string $table, string|array $uniqueBy, ?array $update)
    {
        if (! isset($table)) {
            throw new InvalidArgumentException('Cannot proceed with the seeding. $table is required.');
        }

        if (! Schema::hasTable($table)) {
            $this->command->error("Stopping the operation. The {$table} table doesn't exists.");

            return;
        }

        if ($this->isFileExists($path)) {
            $data = $this->getFileContents($path);

            if ($data->isEmpty()) {
                $this->command->info('Stoping the operation. Nothing to seed.');

                return;
            }

            $this->command->info("Seeding {$data->count()} items on {$table} table.");

            $data->chunk(1000)->each(function (Collection $items) use ($table, $uniqueBy, $update) {
                DB::table($table)->upsert($items->all(), $uniqueBy, $update);
            });

            $this->command->info("Seeded {$data->count()} items on {$table} table.");
        }
    }

    private function isFileExists(string $path, string $message = 'File not found'): bool
    {
        $exists = file_exists($path);

        if (! $exists) {
            $this->command->error($message.' at '.$path);
        }

        return $exists;
    }

    private function getFileContents(string $path): Collection
    {
        $raw = file_get_contents($path);
        $data = json_decode($raw, true);

        if (empty($data)) {
            $this->command->error("The {$path} data are empty.");
        }

        return collect($data ?? []);
    }
}
