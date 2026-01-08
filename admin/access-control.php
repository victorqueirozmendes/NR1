<?php
/**
 * Admin - Controle de Acesso por Usuário
 * /admin/access-control.php
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

verificarAdmin();

$usuario = getUsuarioLogado();
$mensagem = '';
$erro = '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'atualizar') {
        $usuarioId = $_POST['usuario_id'] ?? '';
        $cursoId = $_POST['curso_id'] ?? '';
        $accessLevel = $_POST['access_level'] ?? 'completo';
        $podeVerMateriais = isset($_POST['pode_ver_materiais']) ? 1 : 0;
        
        if ($usuarioId && $cursoId) {
            atualizarPermissaoUsuario($usuarioId, $cursoId, [
                'access_level' => $accessLevel,
                'pode_ver_materiais' => $podeVerMateriais
            ]);
            $mensagem = 'Permissões atualizadas com sucesso!';
        }
    } elseif ($acao === 'remover') {
        $usuarioId = $_POST['usuario_id'] ?? '';
        $cursoId = $_POST['curso_id'] ?? '';
        
        if ($usuarioId && $cursoId) {
            removerAccessoCurso($usuarioId, $cursoId);
            $mensagem = 'Acesso removido com sucesso!';
        }
    } elseif ($acao === 'registrar') {
        $usuarioId = $_POST['usuario_id'] ?? '';
        $cursoId = $_POST['curso_id'] ?? '';
        
        if ($usuarioId && $cursoId) {
            $resultado = registrarUsuarioNoCurso($usuarioId, $cursoId, 'completo');
            if ($resultado['sucesso']) {
                $mensagem = $resultado['mensagem'];
            } else {
                $erro = $resultado['mensagem'];
            }
        }
    }
}

// Buscar cursos
$cursos = getRows($conn, 'SELECT id, titulo FROM cursos ORDER BY titulo ASC', []);

// Buscar usuários aprovados
$usuariosAprovados = getRows($conn, 'SELECT id, nome, email FROM usuarios WHERE ativo = 1 AND role = ? ORDER BY nome ASC', ['aluno']);

// Se um curso foi selecionado, buscar usuários do curso
$cursoSelecionado = $_GET['curso_id'] ?? '';
$usuariosDocurso = [];
if ($cursoSelecionado) {
    $usuariosDocurso = getRows($conn,
        'SELECT uac.*, u.nome, u.email FROM user_access_control uac
         JOIN usuarios u ON uac.usuario_id = u.id
         WHERE uac.curso_id = ?
         ORDER BY u.nome ASC',
        [$cursoSelecionado]
    );
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="Controle de acesso por usuário - NR1 EAD">
    <meta name="theme-color" content="#3498db">
    <title>Controle de Acesso - NR1 EAD</title>
    <link rel="stylesheet" href="/css/style-mobile-first.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a href="/" class="navbar-brand">🎓 NR1 EAD Admin</a>
        <div class="navbar-menu">
            <span>Olá, <strong><?php echo htmlspecialchars($usuario['nome']); ?></strong></span>
            <a href="/logout.php">Sair</a>
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
                    
                    <li>
                        <h4>GERENCIAMENTO</h4>
                    </li>
                    <li><a href="/admin/users.php">👥 Usuários</a></li>
                    <li><a href="/admin/courses.php">📚 Cursos</a></li>
                    <li><a href="/admin/modules.php">📋 Módulos</a></li>
                    <li><a href="/admin/lessons.php">📝 Aulas</a></li>
                    <li><a href="/admin/material-upload.php">📎 Materiais</a></li>
                    
                    <li>
                        <h4>CONTROLE</h4>
                    </li>
                    <li><a href="/admin/access-control.php" class="active">🔐 Acesso de Usuários</a></li>
                </ul>
            </sidebar>

            <!-- Conteúdo Principal -->
            <main class="main-content">
                <h1>🔐 Controle de Acesso por Usuário</h1>
                
                <!-- Alertas -->
                <?php if ($mensagem): ?>
                    <div class="alert alert-success">✓ <?php echo htmlspecialchars($mensagem); ?></div>
                <?php endif; ?>
                
                <?php if ($erro): ?>
                    <div class="alert alert-danger">✗ <?php echo htmlspecialchars($erro); ?></div>
                <?php endif; ?>

                <!-- Seletor de Curso -->
                <div class="card mb-30">
                    <div class="card-header">Selecionar Curso</div>
                    <div class="card-body">
                        <form method="GET" class="form">
                            <div class="form-group">
                                <label for="curso_id">Curso *</label>
                                <select name="curso_id" id="curso_id" onchange="this.form.submit()" required>
                                    <option value="">-- Selecione um curso --</option>
                                    <?php foreach ($cursos as $c): ?>
                                        <option value="<?php echo $c['id']; ?>" <?php echo $cursoSelecionado == $c['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($c['titulo']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if ($cursoSelecionado): ?>
                    <!-- Adicionar usuário ao curso -->
                    <div class="card mb-30">
                        <div class="card-header">➕ Adicionar Usuário ao Curso</div>
                        <div class="card-body">
                            <form method="POST" class="form">
                                <input type="hidden" name="acao" value="registrar">
                                <input type="hidden" name="curso_id" value="<?php echo htmlspecialchars($cursoSelecionado); ?>">

                                <div class="form-group">
                                    <label for="usuario_id">Selecionar Usuário *</label>
                                    <select name="usuario_id" id="usuario_id" required>
                                        <option value="">-- Selecione um usuário --</option>
                                        <?php foreach ($usuariosAprovados as $u): ?>
                                            <?php 
                                            // Verificar se usuário já está no curso
                                            $jaCadastrado = false;
                                            foreach ($usuariosDocurso as $ud) {
                                                if ($ud['usuario_id'] == $u['id']) {
                                                    $jaCadastrado = true;
                                                    break;
                                                }
                                            }
                                            ?>
                                            <?php if (!$jaCadastrado): ?>
                                                <option value="<?php echo $u['id']; ?>">
                                                    <?php echo htmlspecialchars($u['nome'] . ' (' . $u['email'] . ')'); ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    ➕ Adicionar Usuário
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Lista de Usuários no Curso -->
                    <div class="card">
                        <div class="card-header">
                            👥 Usuários no Curso (<?php echo count($usuariosDocurso); ?>)
                        </div>
                        <div class="card-body">
                            <?php if (count($usuariosDocurso) > 0): ?>
                                <div class="table-wrapper">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Usuário</th>
                                                <th>Email</th>
                                                <th>Nível</th>
                                                <th>Ver Materiais</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($usuariosDocurso as $ud): ?>
                                                <tr>
                                                    <td data-label="Usuário">
                                                        <?php echo htmlspecialchars($ud['nome']); ?>
                                                    </td>
                                                    <td data-label="Email">
                                                        <?php echo htmlspecialchars($ud['email']); ?>
                                                    </td>
                                                    <td data-label="Nível">
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="acao" value="atualizar">
                                                            <input type="hidden" name="usuario_id" value="<?php echo $ud['usuario_id']; ?>">
                                                            <input type="hidden" name="curso_id" value="<?php echo htmlspecialchars($cursoSelecionado); ?>">
                                                            <select name="access_level" onchange="this.form.submit()">
                                                                <option value="completo" <?php echo $ud['access_level'] == 'completo' ? 'selected' : ''; ?>>Completo</option>
                                                                <option value="limitado" <?php echo $ud['access_level'] == 'limitado' ? 'selected' : ''; ?>>Limitado</option>
                                                                <option value="bloqueado" <?php echo $ud['access_level'] == 'bloqueado' ? 'selected' : ''; ?>>Bloqueado</option>
                                                            </select>
                                                        </form>
                                                    </td>
                                                    <td data-label="Ver Materiais">
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="acao" value="atualizar">
                                                            <input type="hidden" name="usuario_id" value="<?php echo $ud['usuario_id']; ?>">
                                                            <input type="hidden" name="curso_id" value="<?php echo htmlspecialchars($cursoSelecionado); ?>">
                                                            <input type="hidden" name="access_level" value="<?php echo htmlspecialchars($ud['access_level']); ?>">
                                                            <label style="display: flex; align-items: center; gap: 8px;">
                                                                <input type="checkbox" name="pode_ver_materiais" value="1" <?php echo $ud['pode_ver_materiais'] ? 'checked' : ''; ?> onchange="this.form.submit()">
                                                                <span><?php echo $ud['pode_ver_materiais'] ? '✓ Sim' : '✗ Não'; ?></span>
                                                            </label>
                                                        </form>
                                                    </td>
                                                    <td data-label="Ações">
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="acao" value="remover">
                                                            <input type="hidden" name="usuario_id" value="<?php echo $ud['usuario_id']; ?>">
                                                            <input type="hidden" name="curso_id" value="<?php echo htmlspecialchars($cursoSelecionado); ?>">
                                                            <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('Remover acesso deste usuário?')">
                                                                🗑️ Remover
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center">
                                    Nenhum usuário inscrito neste curso ainda.
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 NR1 EAD. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
