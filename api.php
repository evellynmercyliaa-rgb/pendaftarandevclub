<?php
// Izinkan akses dari mana saja (mengatasi masalah CORS jika pakai Live Server)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Jika browser kirim request OPTIONS (Preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 1. Koneksi Database
$conn = new mysqli("localhost", "root", "", "db_devclub");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Koneksi DB Gagal: " . $conn->connect_error]);
    exit();
}

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'daftar') {
    
    // 2. Ambil Data
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);

    $nis    = trim($input['nis'] ?? $_POST['nis'] ?? '');
    $nama   = trim($input['nama'] ?? $_POST['nama'] ?? '');
    $divisi = trim($input['divisi'] ?? $_POST['divisi'] ?? '');
    $email  = trim($input['email'] ?? $_POST['email'] ?? '');

    if (empty($nis) || empty($nama) || empty($divisi) || empty($email)) {
        echo json_encode(["status" => "error", "message" => "Semua field wajib diisi!"]);
        exit();
    }

    // 3. Simpan Ke Database
    $stmt = $conn->prepare("INSERT INTO pendaftaran (nis, nama, divisi, email) VALUES (?, ?, ?, ?)");
    
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Error Query: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("ssss", $nis, $nama, $divisi, $email);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Selamat! Data berhasil disimpan."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal simpan: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Request harus POST dan gunakan ?action=daftar!"]);
}

$conn->close();
?>