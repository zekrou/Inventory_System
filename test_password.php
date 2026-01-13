<?php
$password = 'password';
$new_hash = '$2y$10$2rPNOcWccYKJFlqt.3epOeEJoz2UjM.Ckn8Zc23vMCVzTcOX7OWVm';

echo "Password: $password<br>";
echo "Hash: $new_hash<br><br>";
echo "Test: " . (password_verify($password, $new_hash) ? '✅ OK' : '❌ FAILED') . "<br>";
?>
