<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Imports\QuestionsImport;
use Maatwebsite\Excel\Facades\Excel;

// Test import file
$filePath = public_path('template.xlsx');

if (!file_exists($filePath)) {
    echo "❌ File tidak ditemukan: $filePath\n";
    exit;
}

echo "✓ File ditemukan: $filePath\n";
echo "✓ Ukuran file: " . filesize($filePath) . " bytes\n\n";

try {
    echo "Mencoba import...\n";
    Excel::import(new QuestionsImport, $filePath);
    echo "✓ Import berhasil!\n";
} catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
    echo "❌ Validation Error:\n";
    $failures = $e->failures();
    foreach ($failures as $failure) {
        echo "  Baris {$failure->row()}: " . implode(', ', $failure->errors()) . "\n";
        echo "  Nilai: " . json_encode($failure->values()) . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
