<?php
require_once __DIR__ . '/../includes/functions.php';
ensureSession();
$_SESSION = [];
session_destroy();
header('Location: admin.php');
exit;
