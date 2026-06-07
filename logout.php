<?php
session_start();
// hapus semua data session
session_unset();
// hancurkan session sepenuhnya dari server
session_destroy();
// redirect ke halaman login setelah logout
header("Location: login.php");
exit;
?>