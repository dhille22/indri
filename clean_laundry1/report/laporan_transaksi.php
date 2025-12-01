<?php
require __DIR__ . '/../config/database.php';
$from=$_GET['from']??date('Y-m-01');
$to=$_GET['to']??date('Y-m-d');
header('Content-Type: application/json');
$stmt=$conn->prepare("SELECT t.id_transaksi, p.nama_pelanggan pelanggan, l.nama_layanan layanan, t.berat_cucian, l.harga_perkg, t.total_biaya, t.tanggal_masuk, t.status FROM transaksi t JOIN pelanggan p ON p.id_pelanggan=t.id_pelanggan JOIN layanan l ON l.id_layanan=t.id_layanan WHERE DATE(t.tanggal_masuk) BETWEEN ? AND ? ORDER BY t.tanggal_masuk DESC");
$stmt&&$stmt->bind_param('ss',$from,$to)&&$stmt->execute();
$res=$stmt?$stmt->get_result():false;
$out=[]; if($res){ while($r=$res->fetch_assoc()){ $total= $r['total_biaya']!==null?$r['total_biaya']:$r['harga_perkg']*$r['berat_cucian']; $r['total']=$total; $out[]=$r; }}
echo json_encode($out);
