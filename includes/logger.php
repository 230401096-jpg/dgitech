<?php
// includes/logger.php
// Simple file logger used by scripts and AI agent
function dg_log(string $message, string $file = null): void {
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $file = $file ?: 'app.log';
    $path = $logDir . '/' . $file;
    $date = date('Y-m-d H:i:s');
    $line = "[{$date}] {$message}\n";
    @file_put_contents($path, $line, FILE_APPEND | LOCK_EX);
}
