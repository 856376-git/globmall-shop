<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3307', 'root', '123456');
$pdo->exec('CREATE DATABASE IF NOT EXISTS globmall CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
echo "Database created OK\n";
