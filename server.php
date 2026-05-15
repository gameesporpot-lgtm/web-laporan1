<?php
header('Content-Type: application/json');

// Konfigurasi Webhook Discord
$webhookUrl = 'https://discord.com/api/webhooks/1504848256398659584/jvgSTnd_14QHuSLl1ABY9JcAqosZmCCeJAVtbYjdmCK7608Qmm0qkatlm0GP4pTn8El1'; // GANTI DENGAN WEBHOOK DISCORD ANDA

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
}

// Validasi data
$requiredFields = ['playerName', 'playerId', 'faction', 'rank', 'dutyTime', 'activity'];
foreach ($requiredFields as $field) {
    if (empty($input[$field])) {
        echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
        exit;
    }
}

// Format embed Discord
$embed = [
    'title' => '📋 New Duty Report',
    'description' => '**Duty Report Baru Diterima!**',
    'color' => 3447003, // Blue color
    'fields' => [
        [
            'name' => '👤 Player',
            'value' => $input['playerName'] . ' (ID: `' . $input['playerId'] . '`)',
            'inline' => true
        ],
        [
            'name' => '🏛️ Faction',
            'value' => $input['faction'],
            'inline' => true
        ],
        [
            'name' => '⭐ Rank',
            'value' => $input['rank'],
            'inline' => true
        ],
        [
            'name' => '⏱️ Durasi',
            'value' => $input['dutyTime'] . ' jam',
            'inline' => true
        ],
        [
            'name' => '📝 Aktivitas',
            'value' => $input['activity'],
            'inline' => false
        ]
    ],
    'footer' => [
        'text' => 'GTA SAMP Duty System • ' . date('d/m/Y H:i:s')
    ],
    'timestamp' => date('c')
];

// Kirim ke Discord Webhook
$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['embeds' => [$embed]]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 204) {
    // Simpan ke database (opsional)
    saveToDatabase($input);
    
    echo json_encode(['success' => true, 'message' => 'Report berhasil dikirim ke Discord']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal mengirim ke Discord']);
}

function saveToDatabase($data) {
    // Opsional: Simpan ke file/database
    $logData = date('Y-m-d H:i:s') . ' | ' . json_encode($data) . "\n";
    file_put_contents('duty_reports.log', $logData, FILE_APPEND | LOCK_EX);
}
?>
