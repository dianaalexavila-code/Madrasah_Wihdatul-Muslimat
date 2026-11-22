<?php
require_login();
$appBase = strpos($_SERVER['SCRIPT_NAME'] ?? '', '/pages/') !== false ? '../' : '';
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($pageTitle ?? 'Dashboard')?> · Madrasah Wihdatul Muslimat</title><link rel="stylesheet" href="<?=$appBase?>assets/css/style.css"></head><body>
<div class="app-shell"><?php require __DIR__.'/includes/sidebar.php';?><main class="main"><header class="topbar"><div><span class="eyebrow">SISTEM ADMINISTRASI</span><b><?=e($pageTitle ?? 'Dashboard')?></b></div><div class="admin-chip">Admin: <?=e($_SESSION['admin_name'] ?? 'Administrator')?></div></header><section class="content">
