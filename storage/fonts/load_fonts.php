<?php

require dirname(__DIR__, 2) . '/vendor/autoload.php';
$app = require_once dirname(__DIR__, 2) . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$fontDir = storage_path('fonts');
$pdf = app('dompdf.wrapper');
$dompdf = $pdf->getDomPDF();
$fontMetrics = $dompdf->getFontMetrics();

$fonts = [
    [['family' => 'Space Grotesk', 'style' => 'normal', 'weight' => '300'], $fontDir . '/SpaceGrotesk-Light.ttf'],
    [['family' => 'Space Grotesk', 'style' => 'normal', 'weight' => 'normal'], $fontDir . '/SpaceGrotesk-Regular.ttf'],
    [['family' => 'Space Grotesk', 'style' => 'normal', 'weight' => '500'], $fontDir . '/SpaceGrotesk-Medium.ttf'],
    [['family' => 'Space Grotesk', 'style' => 'normal', 'weight' => '600'], $fontDir . '/SpaceGrotesk-SemiBold.ttf'],
    [['family' => 'Space Grotesk', 'style' => 'normal', 'weight' => 'bold'], $fontDir . '/SpaceGrotesk-Bold.ttf'],
];

foreach ($fonts as $args) {
    if (is_readable($args[1])) {
        $fontMetrics->registerFont($args[0], 'file://' . $args[1]);
    }
}
