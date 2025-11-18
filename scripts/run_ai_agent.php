<?php
// scripts/run_ai_agent.php
// Runner script untuk dipanggil dari cron. Jalankan dari root workspace: php scripts/run_ai_agent.php

require_once __DIR__ . '/../includes/ai_agent.php';

// Pastikan koneksi $conn tersedia via includes/config.php
// Pastikan OPENAI_API_KEY tersedia (safety)
$apiKey = getenv('OPENAI_API_KEY') ?: null;
if (empty($apiKey)) {
    echo "OPENAI_API_KEY tidak ditemukan. Batalkan eksekusi.\n";
    error_log('scripts/run_ai_agent.php: OPENAI_API_KEY not set');
    exit(2);
}

// Panggil analyzeFinances dan log hasil ke stdout / error log
$result = analyzeFinances($GLOBALS['conn'] ?? null);

if (!empty($result['success'])) {
    echo "analyzeFinances succeeded:\n";
    echo $result['insight'] . "\n";
    exit(0);
} else {
    echo "analyzeFinances failed:\n";
    echo json_encode($result) . "\n";
    exit(1);
}
