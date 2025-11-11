<?php
// includes/ai_agent.php

require_once __DIR__ . '/config.php';

// Fungsi kirim prompt ke OpenAI (contoh)
function callOpenAI($prompt) {
    $apiKey = 'YOUR_OPENAI_API_KEY';
    $endpoint = 'https://api.openai.com/v1/chat/completions';
    $data = [
        "model" => "gpt-3.5-turbo",
        "messages" => [
            ["role"=>"system", "content"=>"You are a business-analyst agent."],
            ["role"=>"user", "content"=>$prompt]
        ],
        "max_tokens" => 150,
    ];
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer ".$apiKey
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Fungsi analisis keuangan dasar
function analyzeFinances() {
    global $conn;
    // contoh: hitung total pendapatan bulan ini
    $stmt = $conn->prepare("SELECT SUM(amount) as total_income FROM financial_records WHERE amount_type='credit' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $income = $row['total_income'] ?? 0;

    $stmt2 = $conn->prepare("SELECT SUM(amount) as total_expense FROM financial_records WHERE amount_type='debit' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $stmt2->execute();
    $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    $expense = $row2['total_expense'] ?? 0;

    $margin = $income - $expense;

    $prompt = "For the platform DGITECH: In the last 30 days the total income was Rp {$income} and total expense was Rp {$expense}. Provide a concise business insight in Indonesian.";
    $res = callOpenAI($prompt);
    $insight = $res['choices'][0]['message']['content'] ?? 'No insight';

    // simpan ke ai_insights
    $stmt3 = $conn->prepare("INSERT INTO ai_insights (scope, reference_id, insight, metadata, created_at) VALUES ('global', NULL, :insight, :meta, NOW())");
    $stmt3->execute([
        ':insight' => $insight,
        ':meta'    => json_encode([
            'income'=>$income,
            'expense'=>$expense,
            'margin'=>$margin
        ])
    ]);
}

// Eksekusi simpel
analyzeFinances();
