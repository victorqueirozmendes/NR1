<?php
/**
 * Gerenciamento de Usuários (Admin)
 * /admin/usuarios.php
 */

require_once __DIR__ . '/../includes/auth.php';

// Verificar se está logado e é admin
verificarAdmin();

$usuario = getUsuarioLogado();
$mensagem = '';
$erro = '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $usuarioId = $_POST['usuario_id'] ?? '';
    
    if ($acao === 'aprovar' && $usuarioId) {
        $resultado = aprovarUsuario($usuarioId);
        if ($resultado['sucesso']) {
            $mensagem = $resultado['mensagem'];
        } else {
            $erro = $resultado['mensagem'];
        }
    } elseif ($acao === 'rejeitar' && $usuarioId) {
        $resultado = rejeitarUsuario($usuarioId);
        if ($resultado['sucesso']) {
            $mensagem = $resultado['mensagem'];
        } else {
            $erro = $resultado['mensagem'];
        }
    }
}

// Buscar usuários pendentes e aprovados
require_once __DIR__ . '/../includes/db.php';

$usuariosPendentes = getUsuariosPendentes();
$usuariosAprovados = getUsuariosAprovados();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="Gerenciar usuários - NR1 EAD">
    <meta name="theme-color" content="#3498db">
    <title>Gerenciar Usuários - NR1 EAD</title>
    <link rel="stylesheet" href="/css/style-mobile-first.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a href="/" class="navbar-brand">NR1 EAD</a>
            <div class="navbar-user">
                <span>Admin: <strong><?php echo htmlspecialchars($usuario['nome']); ?></strong></span>
                <a href="/logout.php" class="btn btn-small btn-secondary">Sair</a>
            </div>
        </div>
    </nav>

    <!-- Container Principal -->
    <div class="container">
        <div class="dashboard-container">
            <!-- Sidebar -->
            <aside class="sidebar">
                <h3>📋 Menu</h3>
                <ul class="sidebar-menu">
                    <li><a href="/dashboard.php">Meu Dashboard</a></li>
                    <li><a href="/">Voltar ao inicio</a></li>
                    <li style="margin-top: 20px; border-top: 1px solid #ecf0f1; padding-top: 15px;">
                        <h4 style="color: #2c3e50; margin-bottom: 10px; font-size: 12px;">ADMINISTRAÇÃO</h4>
                    </li>
                    <li><a href="/admin/usuarios.php" class="active">👥 Gerenciar Usuários</a></li>
                    <li><a href="/admin/cursos.php">📚 Gerenciar Cursos</a></li>
                </ul>
            </aside>

            <!-- Conteúdo Principal -->
            <main class="main-content">
                <h1>👥 Gerenciamento de Usuários</h1>
                
                <?php if ($mensagem): ?>
                    <div class="alert alert-success">
                        ✓ <?php echo htmlspecialchars($mensagem); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($erro): ?>
                    <div class="alert alert-danger">
                        ✗ <?php echo htmlspecialchars($erro); ?>
                    </div>
                <?php endif; ?>

                <!-- Usuários Pendentes de Aprovação -->
                <div class="card mb-30">
                    <div class="card-header">
                        ⏳ Usuários Pendentes de Aprovação
                        <span style="float: right; font-weight: normal;">
                            <?php echo count($usuariosPendentes) > 0 ? count($usuariosPendentes) . ' pendente(s)' : ''; ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <?php if (count($usuariosPendentes) > 0): ?>
                            <div style="overflow-x: auto;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Cadastrado em</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($usuariosPendentes as $usr): ?>
                                            <tr>
                                                <td>#<?php echo htmlspecialchars($usr['id']); ?></td>
                                                <td><?php echo htmlspecialchars($usr['nome']); ?></td>
                                                <td><?php echo htmlspecialchars($usr['email']); ?></td>
                                                <td>
                                                    <?php 
                                                        $data = new DateTime($usr['created_at']);
                                                        echo $data->format('d/m/Y H:i');
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="table-actions">
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="acao" value="aprovar">
                                                            <input type="hidden" name="usuario_id" value="<?php echo htmlspecialchars($usr['id']); ?>">
                                                            <button type="submit" class="btn btn-success btn-small" onclick="return confirm('Aprovar este usuário?')">
                                                                ✓ Aprovar
                                                            </button>
                                                        </form>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="acao" value="rejeitar">
                                                            <input type="hidden" name="usuario_id" value="<?php echo htmlspecialchars($usr['id']); ?>">
                                                            <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('Rejeitar este usuário? Essa ação não pode ser desfeita.')">
                                                                ✗ Rejeitar
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center" style="padding: 20px 0;">
                                ✓ Nenhum usuário pendente de aprovação.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Usuários Aprovados -->
                <div class="card">
                    <div class="card-header">
                        ✓ Usuários Aprovados
                        <span style="float: right; font-weight: normal;">
                            <?php echo count($usuariosAprovados); ?> usuário(s)
                        </span>
                    </div>
                    <div class="card-body">
                        <?php if (count($usuariosAprovados) > 0): ?>
                            <div style="overflow-x: auto;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Função</th>
                                            <th>Cadastrado em</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($usuariosAprovados as $usr): ?>
                                            <tr>
                                                <td>#<?php echo htmlspecialchars($usr['id']); ?></td>
                                                <td><?php echo htmlspecialchars($usr['nome']); ?></td>
                                                <td><?php echo htmlspecialchars($usr['email']); ?></td>
                                                <td>
                                                    <?php if ($usr['role'] === 'admin'): ?>
                                                        <span class="badge badge-info">Admin</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-success">Aluno</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                        $data = new DateTime($usr['created_at']);
                                                        echo $data->format('d/m/Y H:i');
                                                    ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-success">✓ Ativo</span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center" style="padding: 20px 0;">
                                Nenhum usuário aprovado ainda.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background-color: #2c3e50; color: white; text-align: center; padding: 20px; margin-top: 50px;">
        <p>&copy; 2024 NR1 EAD. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
