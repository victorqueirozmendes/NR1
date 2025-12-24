<?php
/**
 * Admin - Gerenciamento de Usuários
 * /admin/users.php
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

// Verificar acesso admin
verificarAdmin();

$usuario = getUsuarioLogado();
$mensagem = '';
$erro = '';
$filtro = $_GET['filtro'] ?? 'pendentes'; // pendentes, ativos, todos

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
    } elseif ($acao === 'promover' && $usuarioId) {
        // Promover usuário a admin
        $resultado = getRow($conn, 
            'UPDATE usuarios SET role = "admin" WHERE id = ? AND role = "aluno"',
            [$usuarioId]
        );
        $mensagem = 'Usuário promovido a administrador!';
    } elseif ($acao === 'rebaixar' && $usuarioId) {
        // Rebaixar admin para aluno
        if ($usuarioId != $usuario['id']) { // Não pode rebaixar a si mesmo
            $resultado = getRow($conn,
                'UPDATE usuarios SET role = "aluno" WHERE id = ? AND role = "admin"',
                [$usuarioId]
            );
            $mensagem = 'Usuário rebaixado para aluno!';
        } else {
            $erro = 'Você não pode rebaixar sua própria conta!';
        }
    }
}

// Buscar usuários conforme filtro
if ($filtro === 'pendentes') {
    $usuarios = getUsuariosPendentes();
    $titulo = '⏳ Usuários Pendentes de Aprovação';
} elseif ($filtro === 'ativos') {
    $usuarios = getUsuariosAprovados();
    $titulo = '✓ Usuários Aprovados';
} else {
    $usuarios = getRows($conn, 'SELECT * FROM usuarios ORDER BY created_at DESC', []);
    $titulo = '👥 Todos os Usuários';
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="Gerenciar usuários e aprovar novos alunos - NR1 EAD">
    <meta name="theme-color" content="#3498db">
    <title>Gerenciar Usuários - NR1 EAD</title>
    <link rel="stylesheet" href="/css/style-mobile-first.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a href="/" class="navbar-brand">🎓 NR1 EAD Admin</a>
            <div class="navbar-user">
                <span>Olá, <strong><?php echo htmlspecialchars($usuario['nome']); ?></strong></span>
                <a href="/logout.php" class="btn btn-small btn-secondary">Sair</a>
            </div>
        </div>
    </nav>

    <!-- Container Principal -->
    <div class="container">
        <div class="dashboard-container">
            <!-- Sidebar -->
            <aside class="sidebar">
                <h3>📋 PAINEL ADMIN</h3>
                <ul class="sidebar-menu">
                    <li><a href="/dashboard.php">📊 Dashboard</a></li>
                    <li><a href="/">🏠 Voltar ao Site</a></li>
                    
                    <li style="margin-top: 20px; border-top: 1px solid #ecf0f1; padding-top: 15px;">
                        <h4 style="color: #2c3e50; margin-bottom: 10px; font-size: 12px;">GERENCIAMENTO</h4>
                    </li>
                    <li><a href="/admin/users.php" class="active">👥 Usuários</a></li>
                    <li><a href="/admin/courses.php">📚 Cursos</a></li>
                    <li><a href="/admin/modules.php">📋 Módulos</a></li>
                    <li><a href="/admin/lessons.php">📝 Aulas</a></li>
                    <li><a href="/admin/material-upload.php">📎 Materiais</a></li>
                </ul>
            </aside>

            <!-- Conteúdo Principal -->
            <main class="main-content">
                <h1><?php echo $titulo; ?></h1>
                
                <!-- Alertas -->
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

                <!-- Abas de Filtro -->
                <div class="filter-tabs" style="margin-bottom: 20px;">
                    <a href="?filtro=pendentes" class="tab-btn <?php echo $filtro === 'pendentes' ? 'active' : ''; ?>">
                        ⏳ Pendentes
                    </a>
                    <a href="?filtro=ativos" class="tab-btn <?php echo $filtro === 'ativos' ? 'active' : ''; ?>">
                        ✓ Aprovados
                    </a>
                    <a href="?filtro=todos" class="tab-btn <?php echo $filtro === 'todos' ? 'active' : ''; ?>">
                        👥 Todos
                    </a>
                </div>

                <!-- Estatísticas -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px;">
                    <div class="stat-box">
                        <div class="stat-number"><?php echo count(getUsuariosPendentes()); ?></div>
                        <div class="stat-label">Pendentes</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number"><?php echo count(getUsuariosAprovados()); ?></div>
                        <div class="stat-label">Aprovados</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number"><?php echo count($usuarios); ?></div>
                        <div class="stat-label">Exibindo</div>
                    </div>
                </div>

                <!-- Tabela de Usuários -->
                <div class="card">
                    <div class="card-header">
                        Usuários (<?php echo count($usuarios); ?>)
                    </div>
                    <div class="card-body">
                        <?php if (count($usuarios) > 0): ?>
                            <div style="overflow-x: auto;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Função</th>
                                            <th>Status</th>
                                            <th>Cadastrado em</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($usuarios as $usr): ?>
                                            <tr>
                                                <td>#<?php echo htmlspecialchars($usr['id']); ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($usr['nome']); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($usr['email']); ?></td>
                                                <td>
                                                    <?php if ($usr['role'] === 'admin'): ?>
                                                        <span class="badge badge-info">👑 Admin</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-success">👤 Aluno</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($usr['ativo'] == 1): ?>
                                                        <span class="badge badge-success">✓ Ativo</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning">⏳ Pendente</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="font-size: 12px;">
                                                    <?php 
                                                        $data = new DateTime($usr['created_at']);
                                                        echo $data->format('d/m/Y H:i');
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="table-actions" style="flex-wrap: wrap;">
                                                        <!-- Se pendente: Aprovar/Rejeitar -->
                                                        <?php if ($usr['ativo'] == 0): ?>
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
                                                                <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('Rejeitar? Não pode desfazer!')">
                                                                    ✗ Rejeitar
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>

                                                        <!-- Se ativo: Promover/Rebaixar -->
                                                        <?php if ($usr['ativo'] == 1 && $usr['id'] != $usuario['id']): ?>
                                                            <?php if ($usr['role'] === 'aluno'): ?>
                                                                <form method="POST" style="display: inline;">
                                                                    <input type="hidden" name="acao" value="promover">
                                                                    <input type="hidden" name="usuario_id" value="<?php echo htmlspecialchars($usr['id']); ?>">
                                                                    <button type="submit" class="btn btn-warning btn-small" onclick="return confirm('Promover a admin?')">
                                                                        👑 Promover
                                                                    </button>
                                                                </form>
                                                            <?php else: ?>
                                                                <form method="POST" style="display: inline;">
                                                                    <input type="hidden" name="acao" value="rebaixar">
                                                                    <input type="hidden" name="usuario_id" value="<?php echo htmlspecialchars($usr['id']); ?>">
                                                                    <button type="submit" class="btn btn-warning btn-small" onclick="return confirm('Rebaixar para aluno?')">
                                                                        👤 Rebaixar
                                                                    </button>
                                                                </form>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center" style="padding: 40px 0;">
                                Nenhum usuário nesta categoria.
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

    <style>
        .filter-tabs {
            display: flex;
            gap: 10px;
            border-bottom: 2px solid #ecf0f1;
        }

        .tab-btn {
            padding: 10px 20px;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-weight: 600;
            color: #7f8c8d;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .tab-btn:hover,
        .tab-btn.active {
            color: #3498db;
            border-bottom-color: #3498db;
        }

        .stat-box {
            background: white;
            border: 1px solid #ecf0f1;
            padding: 20px;
            border-radius: 4px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #3498db;
        }

        .stat-label {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 8px;
        }
    </style>
</body>
</html>
