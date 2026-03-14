<?php

namespace App\Console\Commands;

use FontLib\Font;
use Illuminate\Console\Command;

class RegisterDomPdfFonts extends Command
{
    protected $signature = 'fonts:register';

    protected $description = 'Register Space Grotesk font weights with DomPDF';

    public function handle(): int
    {
        $fontDir = storage_path('fonts');

        if (!is_dir($fontDir)) {
            $this->error("Font directory does not exist: {$fontDir}");
            return self::FAILURE;
        }

        $weights = [
            '300' => 'SpaceGrotesk-Light',
            'normal' => 'SpaceGrotesk-Regular',
            '500' => 'SpaceGrotesk-Medium',
            '600' => 'SpaceGrotesk-SemiBold',
            'bold' => 'SpaceGrotesk-Bold',
        ];

        $entry = [];

        foreach ($weights as $type => $baseName) {
            $path = $fontDir . DIRECTORY_SEPARATOR . $baseName . '.ttf';
            if (!is_readable($path)) {
                $this->warn("Skipping missing file: {$path}");
                continue;
            }

            try {
                $font = Font::load($path);
                $font->parse();
                $ufmPath = $fontDir . DIRECTORY_SEPARATOR . $baseName . '.ufm';
                $font->saveAdobeFontMetrics($ufmPath);
                $font->close();
                $entry[$type] = $baseName;
                $this->info("Registered: {$baseName}");
            } catch (\Throwable $e) {
                $this->error("Failed {$baseName}: " . $e->getMessage());
            }
        }

        if (empty($entry)) {
            $this->error('No font files were registered. Place Space Grotesk TTF files in storage/fonts/.');
            return self::FAILURE;
        }

        $pdf = app('dompdf.wrapper');
        $dompdf = $pdf->getDomPDF();
        $fontMetrics = $dompdf->getFontMetrics();
        $fontMetrics->setFontFamily('space grotesk', $entry);

        $this->info('Space Grotesk font family registered with DomPDF.');
        return self::SUCCESS;
    }
}
