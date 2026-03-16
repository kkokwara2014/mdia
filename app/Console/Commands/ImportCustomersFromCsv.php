<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ImportCustomersFromCsv extends Command
{
    protected $signature = 'customers:import 
                            {file : Path to the CSV file}
                            {--domain=mdia.member : Email domain for generated emails}
                            {--dry-run : Parse CSV and show what would be imported without writing to DB}';

    protected $description = 'Import customers from CSV (SN, NAMES, Country of Residence, Phone Number, Registration Year). Generates unique emails and phones when missing.';

    protected string $emailDomain = 'mdia.member';

    protected array $usedEmails = [];

    protected array $usedPhones = [];

    public function handle(): int
    {
        $path = $this->argument('file');
        $this->emailDomain = $this->option('domain');
        $dryRun = $this->option('dry-run');

        if (!is_readable($path)) {
            $this->error("Cannot read file: {$path}");
            return self::FAILURE;
        }

        $this->usedEmails = User::pluck('email')->map(fn ($e) => strtolower($e))->flip()->all();
        $this->usedPhones = User::pluck('phone')->flip()->all();

        $rows = $this->parseCsv($path);
        if (empty($rows)) {
            $this->error('No valid rows found in CSV.');
            return self::FAILURE;
        }

        $this->info(sprintf('Found %d row(s). Columns: %s', count($rows), implode(', ', array_keys($rows[0]))));
        $this->newLine();

        if ($dryRun) {
            $this->warn('DRY RUN – no changes will be made.');
            $this->newLine();
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $lineNum = $index + 2; // 1-indexed + header
            $result = $this->processRow($row, $dryRun, $lineNum);
            if ($result === true) {
                $imported++;
                $this->line("<info>✓</info> Line {$lineNum}: {$row['NAMES']}");
            } elseif ($result === false) {
                $skipped++;
            } else {
                $errors[] = "Line {$lineNum}: {$result}";
            }
        }

        $this->newLine();
        $this->info("Imported: {$imported} | Skipped: {$skipped}");
        if (!empty($errors)) {
            $this->newLine();
            $this->error('Errors:');
            foreach ($errors as $err) {
                $this->line("  • {$err}");
            }
        }

        return empty($errors) ? self::SUCCESS : self::FAILURE;
    }

    protected function parseCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            return [];
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return [];
        }

        $header = array_map('trim', $header);
        $required = ['SN', 'NAMES'];
        foreach ($required as $col) {
            if (!in_array($col, $header, true)) {
                $this->error("CSV must have column: {$col}");
                fclose($handle);
                return [];
            }
        }

        $countryCol = $this->findColumn($header, ['Country of Residence', 'Country']);
        $phoneCol = $this->findColumn($header, ['Phone Number', 'Phone']);
        $yearCol = $this->findColumn($header, ['Registration Year', 'Year']);

        $rows = [];
        while (($raw = fgetcsv($handle)) !== false) {
            $values = array_slice(array_pad(array_map('trim', $raw), count($header), ''), 0, count($header));
            $row = array_combine($header, $values);
            $name = $row['NAMES'] ?? '';
            if (trim($name) === '') {
                continue;
            }
            $row['_country_col'] = $countryCol;
            $row['_phone_col'] = $phoneCol;
            $row['_year_col'] = $yearCol;
            $rows[] = $row;
        }
        fclose($handle);

        return $rows;
    }

    protected function findColumn(array $header, array $candidates): ?string
    {
        foreach ($candidates as $c) {
            if (in_array($c, $header, true)) {
                return $c;
            }
        }
        return null;
    }

    protected function processRow(array $row, bool $dryRun, int $lineNum): bool|string
    {
        $name = trim($row['NAMES'] ?? '');
        $country = $row['_country_col'] ? trim($row[$row['_country_col']] ?? '') : null;
        $phoneRaw = $row['_phone_col'] ? trim($row[$row['_phone_col']] ?? '') : '';
        $yearRaw = $row['_year_col'] ? trim($row[$row['_year_col']] ?? '') : null;
        $year = $this->normalizeRegistrationYear($yearRaw);

        $phone = $this->resolvePhone($phoneRaw);
        if ($phone === null) {
            return 'Could not generate unique phone number after retries';
        }

        $email = $this->generateEmail($name);
        if ($email === null) {
            return 'Could not generate unique email';
        }

        if (User::where('email', $email)->orWhere('phone', $phone)->exists()) {
            return 'Email or phone already exists in database';
        }

        if ($dryRun) {
            $this->line("  Would create: {$name} | {$email} | {$phone}");
            return true;
        }

        $plainPassword = $this->generatePassword();

        User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($plainPassword),
            'country_of_residence' => $country ?: 'United States of America',
            'registration_year' => $year,
        ]);

        return true;
    }

    protected function normalizeRegistrationYear(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (preg_match('/\b(19|20)\d{2}\b/', $value, $m)) {
            return $m[0];
        }
        return null;
    }

    protected function resolvePhone(?string $raw): ?string
    {
        $cleaned = preg_replace('/[^\d+\-\s()]/', '', $raw ?? '');
        if ($cleaned !== '' && strlen($cleaned) >= 7) {
            $phone = preg_replace('/\s+/', ' ', trim($cleaned));
            if (!$this->isPhoneUsed($phone)) {
                $this->markPhoneUsed($phone);
                return $phone;
            }
        }

        for ($i = 0; $i < 50; $i++) {
            $phone = $this->generateUniquePhone();
            if ($phone !== null) {
                return $phone;
            }
        }
        return null;
    }

    protected function generateUniquePhone(): ?string
    {
        $digits = str_pad((string) random_int(0, 9999999999), 10, '0', STR_PAD_LEFT);
        $phone = $digits;
        if ($this->isPhoneUsed($phone)) {
            return null;
        }
        $this->markPhoneUsed($phone);
        return $phone;
    }

    protected function isPhoneUsed(string $phone): bool
    {
        return isset($this->usedPhones[$phone]) || User::where('phone', $phone)->exists();
    }

    protected function markPhoneUsed(string $phone): void
    {
        $this->usedPhones[$phone] = true;
    }

    protected function generateEmail(string $name): ?string
    {
        $base = $this->nameToEmailBase($name);
        if ($base === '') {
            return null;
        }

        $candidate = $base . '@' . $this->emailDomain;
        $attempt = 0;
        while ($this->isEmailUsed($candidate)) {
            $attempt++;
            $suffix = $attempt <= 10 ? (string) $attempt : bin2hex(random_bytes(2));
            $candidate = $base . '.' . $suffix . '@' . $this->emailDomain;
        }
        $this->markEmailUsed($candidate);
        return $candidate;
    }

    protected function nameToEmailBase(string $name): string
    {
        $words = preg_split('/\s+/u', trim($name), -1, PREG_SPLIT_NO_EMPTY);
        if (empty($words)) {
            return '';
        }
        $normalized = array_map(function ($w) {
            return preg_replace('/[^a-z0-9]/', '', strtolower($w));
        }, $words);
        $normalized = array_filter($normalized);
        if (empty($normalized)) {
            return 'user' . bin2hex(random_bytes(3));
        }
        return implode('.', $normalized);
    }

    protected function isEmailUsed(string $email): bool
    {
        $key = strtolower($email);
        return isset($this->usedEmails[$key]) || User::where('email', $email)->exists();
    }

    protected function markEmailUsed(string $email): void
    {
        $this->usedEmails[strtolower($email)] = true;
    }

    protected function generatePassword(): string
    {
        $uppercase = chr(random_int(65, 90));
        $lowercase = substr(str_shuffle('abcdefghjkmnpqrstuvwxyz'), 0, 4);
        $numbers = substr(str_shuffle('23456789'), 0, 2);
        $special = ['@', '#', '$', '!'][random_int(0, 3)];
        return str_shuffle($uppercase . $lowercase . $numbers . $special);
    }
}
