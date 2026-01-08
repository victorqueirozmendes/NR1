<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

verificarLogin();

$usuario = getUsuarioLogado();
$ehAdmin = ehAdmin();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Dashboard - NR1</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="/css/style-mobile-first.css?v=2025122419">
</head>

<body>

<!-- NAVBAR -->
    <nav class="navbar">
        <a href="/" class="navbar-brand">NR1 EAD</a>
        <div class="navbar-menu">
            <span>Bem-vindo, <strong><?php echo htmlspecialchars($usuario['nome']); ?></strong></span>
            <a href="/logout.php">Sair</a>
        </div>
    </nav>

<div class="container">
<div class="dashboard-container">

<!-- SIDEBAR -->
<aside class="sidebar">
    <h3>📋 Menu</h3>
    <ul class="sidebar-menu">
        <li><a href="/dashboard.php" class="active">🏠 Meu Dashboard</a></li>

        <!-- 🔥 LINK CORRIGIDO -->
        <li><a href="/admin/material-upload.php">📂 Materiais</a></li>

        <li><a href="/">🌐 Site</a></li>

        <?php if ($ehAdmin): ?>
        <li style="margin-top:20px;border-top:1px solid #ddd;padding-top:15px;font-size:12px;color:#555;">
            ADMIN
        </li>
        <li><a href="/admin/usuarios.php">👥 Usuários</a></li>
        <li><a href="/admin/cursos.php">📚 Cursos</a></li>
        <?php endif; ?>
    </ul>
</aside>

<!-- CONTEÚDO -->
<main class="main-content">

<h1>Dashboard</h1>

<div class="alert alert-info">
Bem-vindo, <strong><?php echo htmlspecialchars($usuario['nome']); ?></strong>.
Você está logado como 
<strong><?php echo $ehAdmin ? "Administrador" : "Aluno"; ?></strong>.
</div>

<!-- CARD USUÁRIO -->
<div class="card">
<div class="card-header">👤 Minha Conta</div>
<div class="card-body">

<table class="table">
<tr>
<td>Nome</td>
<td><?php echo htmlspecialchars($usuario['nome']); ?></td>
</tr>

<tr>
<td>Email</td>
<td><?php echo htmlspecialchars($usuario['email']); ?></td>
</tr>

<tr>
<td>Tipo</td>
<td><?php echo $ehAdmin ? "Administrador" : "Aluno"; ?></td>
</tr>
</table>

</div>
</div>

<!-- CURSOS -->
<div class="card mt-20">
<div class="card-header">📚 Meus Cursos</div>
<div class="card-body">
<p style="color:#777;text-align:center;padding:20px">
Nenhum curso disponível no momento.
</p>
</div>
</div>

<?php if ($ehAdmin): ?>
<!-- ADMIN -->
<div class="card mt-30">
<div class="card-header">⚙️ Administração</div>
<div class="card-body">

<div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
    <a href="/admin/usuarios.php" class="btn btn-secondary">Gerenciar Usuários</a>
    <a href="/admin/cursos.php" class="btn btn-secondary">Gerenciar Cursos</a>

    <!-- 🔥 BOTÃO CORRIGIDO -->
    <a href="/admin/material-upload.php" class="btn btn-secondary">
        Upload de Materiais
    </a>
</div>

</div>
</div>
<?php endif; ?>

</main>
</div>
</div>

<footer style="background:#2c3e50;color:white;text-align:center;padding:20px;margin-top:40px">
&copy; 2024 NR1 EAD
</footer>

</body>
</html>
