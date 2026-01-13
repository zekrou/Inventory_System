<?php
echo "Langue du navigateur: " . substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', 0, 2);
echo "<br>";
echo "HTTP_ACCEPT_LANGUAGE complet: " . ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'non défini');
?>
