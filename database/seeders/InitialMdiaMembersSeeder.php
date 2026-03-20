<?php

namespace Database\Seeders;

use App\Services\CustomerCsvImporter;
use Illuminate\Database\Seeder;

class InitialMdiaMembersSeeder extends Seeder
{
    /**
     * Import members from initial_mdia_data.csv (project root).
     * Same behavior as the former `customers:import` artisan command for that file.
     */
    public function run(): void
    {
        $path = base_path('initial_mdia_data.csv');

        if (! is_readable($path)) {
            throw new \RuntimeException("Cannot read file: {$path}");
        }

        $importer = (new CustomerCsvImporter)->setEmailDomain('mdia.member');

        $output = $this->command?->getOutput();

        try {
            $result = $importer->import($path, false, $output);
        } catch (\InvalidArgumentException $e) {
            if ($this->command) {
                $this->command->error($e->getMessage());
            }
            throw $e;
        }

        if (! empty($result['errors'])) {
            throw new \RuntimeException(
                'Initial MDIA member import failed: '.implode('; ', $result['errors'])
            );
        }
    }
}
