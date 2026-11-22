<?php
require dirname(__DIR__).'/config.php'; require dirname(__DIR__).'/functions.php'; require_login();
header('Content-Type: application/json; charset=utf-8');
$year=(int)($_GET['year']??date('Y')); if($year<2000||$year>2100){http_response_code(400);echo json_encode(['error'=>'Tahun tidak valid']);exit;}
$prefix='WM'.$year;
$stmt=$pdo->prepare("SELECT nomor_induk FROM students WHERE nomor_induk LIKE ? ORDER BY id DESC LIMIT 1");$stmt->execute([$prefix.'-%']);$last=(string)$stmt->fetchColumn();
$sequence=1;if(preg_match('/-(\d+)$/',$last,$match))$sequence=(int)$match[1]+1;
echo json_encode(['number'=>$prefix.'-'.str_pad((string)$sequence,4,'0',STR_PAD_LEFT)]);
