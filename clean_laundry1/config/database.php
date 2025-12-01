<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_clean_laundry';
$conn = @new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo 'Koneksi database gagal';
    exit;
}
$conn->set_charset('utf8mb4');
