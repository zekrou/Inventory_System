<?php
session_start();
session_destroy();
header('Location: http://localhost/inventorysysmulti/auth/login');
exit;
?>
