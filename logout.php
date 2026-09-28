<?php
session_start();
unset($_SESSION['usuarioLogado']);
unset($_SESSION['tipoUser']);
session_destroy();
header('Location: login.php');
exit;
?>