<?php
// includes/ai_agent.php

require_once __DIR__ . '/config.php';

// Kirim prompt ke OpenAI dengan penanganan error yang lebih baik
function callOpenAI(string $prompt, string $model = 'gpt-3.5-turbo', int $max_tokens = 150): array {
    $apiKey = getenv('OPENAI_API_KEY') ?: null;
    if (empty($apiKey)) {
        error_log('OpenAI API key not set (OPENAI_API_KEY)');
        return ['success' => false, 'error' => 'missing_api_key'];
    }

    $endpoint = 'https://api.openai.com/v1/chat/completions';
    $data = [
        'model' => $model,
        'messages' => [
            ['role' => 'system', 'content' => 'You are a business-analyst agent.'],
            ['role' => 'user', 'content' => $prompt]
        ],
        'max_tokens' => $max_tokens,
    ];

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $response = curl_exec($ch);
    if ($response === false) {
        $err = curl_error($ch);
        curl_close($ch);
        error_log('cURL error when calling OpenAI: ' . $err);
        return ['success' => false, 'error' => 'curl_error', 'message' => $err];
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON decode error from OpenAI response: ' . json_last_error_msg());
        return ['success' => false, 'error' => 'json_decode_error'];
    }

    if ($httpCode < 200 || $httpCode >= 300) {
        $errMsg = $decoded['error']['message'] ?? 'OpenAI returned HTTP ' . $httpCode;
        error_log('OpenAI API error: ' . $errMsg);
        return ['success' => false, 'error' => 'api_error', 'message' => $errMsg, 'http_code' => $httpCode, 'raw' => $decoded];
    }

    return ['success' => true, 'data' => $decoded, 'http_code' => $httpCode];
}

// Fungsi analisis keuangan dasar — tidak dieksekusi otomatis
function analyzeFinances(PDO $pdo = null): array {
    $connLocal = $pdo ?? ($GLOBALS['conn'] ?? null);
    if (!($connLocal instanceof PDO)) {
        error_log('No PDO connection available for analyzeFinances');
        return ['success' => false, 'error' => 'no_db_connection'];
    }

    try {
        $stmt = $connLocal->prepare("SELECT SUM(amount) as total_income FROM financial_records WHERE amount_type='credit' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $income = (float) ($row['total_income'] ?? 0);

        $stmt2 = $connLocal->prepare("SELECT SUM(amount) as total_expense FROM financial_records WHERE amount_type='debit' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $stmt2->execute();
        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        $expense = (float) ($row2['total_expense'] ?? 0);

        $margin = $income - $expense;

        $prompt = "Untuk platform DGITECH: Dalam 30 hari terakhir total pendapatan Rp {$income} dan total biaya Rp {$expense}. Berikan insight bisnis singkat dalam Bahasa Indonesia.";
        $res = callOpenAI($prompt);
        if (empty($res['success'])) {
            return ['success' => false, 'error' => 'openai_failed', 'details' => $res];
        }

        $content = $res['data']['choices'][0]['message']['content'] ?? null;
        if (empty($content)) {
            return ['success' => false, 'error' => 'no_insight_returned', 'raw' => $res['data'] ?? null];
        }

        // Siapkan metadata, tambahkan usage OpenAI jika tersedia
        $meta = ['income' => $income, 'expense' => $expense, 'margin' => $margin];
        if (!empty($res['data']['usage'])) {
            $meta['openai_usage'] = $res['data']['usage'];
        }

        // simpan ke ai_insights
        $stmt3 = $connLocal->prepare("INSERT INTO ai_insights (scope, reference_id, insight, metadata, created_at) VALUES ('global', NULL, :insight, :meta, NOW())");
        $stmt3->execute([
            ':insight' => $content,
            ':meta'    => json_encode($meta)
        ]);

        return ['success' => true, 'insight' => $content];
    } catch (Exception $e) {
        error_log('analyzeFinances error: ' . $e->getMessage());
        return ['success' => false, 'error' => 'exception', 'message' => $e->getMessage()];
    }
}

// Catatan: fungsi tidak dieksekusi otomatis. Panggil `analyzeFinances()` dari controller atau cron job.
