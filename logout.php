<?php
/**
 * Logout
 * /logout.php
 */

require_once __DIR__ . '/includes/auth.php';

logout();

// Redirecionar para login
header('Location: /login.php?mensagem=logout_sucesso');
exit;
