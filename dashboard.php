<?php
/**
 * Dashboard do Usuário
 * /dashboard.php
 */

require_once __DIR__ . '/includes/auth.php';

// Verificar se está logado
verificarLogin();

$usuario = getUsuarioLogado();
$ehAdmin = ehAdmin();

// Se é admin, adicionar link para gerenciamento
$adminPanel = '';
if ($ehAdmin) {
    $adminPanel = 'admin/usuarios.php';
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="Dashboard - NR1 EAD">
    <meta name="theme-color" content="#3498db">
    <title>Dashboard - NR1 EAD</title>
    <link rel="stylesheet" href="/css/style-mobile-first.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a href="/" class="navbar-brand">NR1 EAD</a>
        <div class="navbar-menu">
            <span>Bem-vindo, <strong><?php echo htmlspecialchars($usuario['nome']); ?></strong></span>
            <a href="/logout.php">Sair</a>
        </div>
    </nav>

    <!-- Container Principal -->
    <div class="container">
        <div class="dashboard-container">
            <!-- Sidebar -->
            <aside class="sidebar">
                <h3>📋 Menu</h3>
                <ul class="sidebar-menu">
                    <li><a href="/dashboard.php" class="active">Meu Dashboard</a></li>
                    <li><a href="/">Voltar ao inicio</a></li>
                    
                    <?php if ($ehAdmin): ?>
                        <li>
                            <h4>ADMINISTRAÇÃO</h4>
                        </li>
                        <li><a href="/admin/usuarios.php">👥 Gerenciar Usuários</a></li>
                        <li><a href="/admin/cursos.php">📚 Gerenciar Cursos</a></li>
                    <?php endif; ?>
                </ul>
            </aside>

            <!-- Conteúdo Principal -->
            <main class="main-content">
                <h1>Dashboard</h1>
                
                <div class="alert alert-info">
                    Olá <?php echo htmlspecialchars($usuario['nome']); ?>! 
                    <?php if ($usuario['role'] === 'admin'): ?>
                        Você está logado como <strong>Administrador</strong>.
                    <?php else: ?>
                        Você está logado como <strong>Aluno</strong>.
                    <?php endif; ?>
                </div>

                <!-- Informações do Usuário -->
                <div class="card mb-20">
                    <div class="card-header">
                        👤 Minhas Informações
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <td><strong>Nome:</strong></td>
                                <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Função:</strong></td>
                                <td>
                                    <?php if ($usuario['role'] === 'admin'): ?>
                                        <span class="badge badge-info">Administrador</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Aluno</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <?php if ($usuario['ativo'] == 1): ?>
                                        <span class="badge badge-success">✓ Ativo</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">⏳ Pendente de Aprovação</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Cadastrado em:</strong></td>
                                <td>
                                    <?php 
                                        $data = new DateTime($usuario['created_at']);
                                        echo $data->format('d/m/Y H:i');
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Seção Cursos (Futuro) -->
                <div class="card">
                    <div class="card-header">
                        📚 Meus Cursos
                    </div>
                    <div class="card-body">
                        <p class="text-muted text-center">
                            Nenhum curso disponível no momento.
                        </p>
                    </div>
                </div>

                <!-- Seção Admin -->
                <?php if ($ehAdmin): ?>
                    <div class="card mt-30">
                        <div class="card-header">
                            ⚙️ Painel Administrativo
                        </div>
                        <div class="card-body">
                            <p>
                                Você tem acesso às seguintes ferramentas administrativas:
                            </p>
                            <div>
                                <a href="/admin/usuarios.php" class="btn btn-secondary btn-block">
                                    👥 Gerenciar Usuários
                                </a>
                                <a href="/admin/cursos.php" class="btn btn-secondary btn-block">
                                    📚 Gerenciar Cursos
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 NR1 EAD. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
