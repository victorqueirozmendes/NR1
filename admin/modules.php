<?php
/**
 * Admin - Gerenciamento de Módulos
 * /admin/modules.php
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

verificarAdmin();

$usuario = getUsuarioLogado();
$mensagem = '';
$erro = '';

// Buscar curso selecionado
$cursoId = $_GET['curso_id'] ?? $_POST['curso_id'] ?? '';
$cursoAtual = null;

if ($cursoId) {
    $cursoAtual = getRow($conn, 'SELECT * FROM cursos WHERE id = ?', [$cursoId]);
} else {
    // Se não tem curso, buscar o primeiro
    $cursoAtual = getRow($conn, 'SELECT * FROM cursos LIMIT 1', []);
    if ($cursoAtual) {
        $cursoId = $cursoAtual['id'];
    }
}

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'criar') {
        $titulo = $_POST['titulo'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $ordem = $_POST['ordem'] ?? 1;
        
        if (empty($titulo)) {
            $erro = 'Título é obrigatório!';
        } else {
            executeQuery($conn,
                'INSERT INTO modulos (curso_id, titulo, descricao, ordem, created_at) VALUES (?, ?, ?, ?, NOW())',
                [$cursoId, $titulo, $descricao, $ordem]
            );
            $mensagem = 'Módulo criado com sucesso!';
        }
    } elseif ($acao === 'editar') {
        $moduloId = $_POST['modulo_id'] ?? '';
        $titulo = $_POST['titulo'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $ordem = $_POST['ordem'] ?? 1;
        
        if (empty($titulo)) {
            $erro = 'Título é obrigatório!';
        } else {
            executeQuery($conn,
                'UPDATE modulos SET titulo = ?, descricao = ?, ordem = ? WHERE id = ?',
                [$titulo, $descricao, $ordem, $moduloId]
            );
            $mensagem = 'Módulo atualizado!';
        }
    } elseif ($acao === 'deletar') {
        $moduloId = $_POST['modulo_id'] ?? '';
        executeQuery($conn, 'DELETE FROM modulos WHERE id = ?', [$moduloId]);
        $mensagem = 'Módulo deletado!';
    }
}

// Buscar módulos do curso
$modulos = [];
if ($cursoId) {
    $modulos = getRows($conn, 
        'SELECT * FROM modulos WHERE curso_id = ? ORDER BY ordem ASC',
        [$cursoId]
    );
}

// Verificar qual módulo está sendo editado
$moduloEditando = null;
if (isset($_GET['editar'])) {
    $moduloEditando = getRow($conn, 'SELECT * FROM modulos WHERE id = ?', [$_GET['editar']]);
}

// Buscar todos os cursos para seletor
$todosOsCursos = getRows($conn, 'SELECT id, titulo FROM cursos ORDER BY titulo ASC', []);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="Gerenciar módulos de cursos - NR1 EAD">
    <meta name="theme-color" content="#3498db">
    <title>Gerenciar Módulos - NR1 EAD</title>
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
                    <li><a href="/admin/users.php">👥 Usuários</a></li>
                    <li><a href="/admin/courses.php">📚 Cursos</a></li>
                    <li><a href="/admin/modules.php" class="active">📋 Módulos</a></li>
                    <li><a href="/admin/lessons.php">📝 Aulas</a></li>
                    <li><a href="/admin/material-upload.php">📎 Materiais</a></li>
                </ul>
            </sidebar>

            <!-- Conteúdo Principal -->
            <main class="main-content">
                <h1>📋 Gerenciar Módulos</h1>
                
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
                        <form method="GET" style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <select name="curso_id" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid #bdc3c7; border-radius: 4px; flex: 1; min-width: 200px;">
                                <?php foreach ($todosOsCursos as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php echo $cursoId == $c['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($c['titulo']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                </div>

                <?php if ($cursoAtual): ?>
                    <!-- Formulário Criar/Editar Módulo -->
                    <div class="card mb-30">
                        <div class="card-header">
                            <?php echo $moduloEditando ? '✏️ Editar Módulo' : '➕ Novo Módulo'; ?>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="form">
                                <input type="hidden" name="acao" value="<?php echo $moduloEditando ? 'editar' : 'criar'; ?>">
                                <input type="hidden" name="curso_id" value="<?php echo $cursoId; ?>">
                                <?php if ($moduloEditando): ?>
                                    <input type="hidden" name="modulo_id" value="<?php echo htmlspecialchars($moduloEditando['id']); ?>">
                                <?php endif; ?>

                                <div class="form-group">
                                    <label>Curso: <strong><?php echo htmlspecialchars($cursoAtual['titulo']); ?></strong></label>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 100px; gap: 15px;">
                                    <div class="form-group">
                                        <label for="titulo">Título do Módulo *</label>
                                        <input 
                                            type="text" 
                                            id="titulo" 
                                            name="titulo" 
                                            placeholder="Ex: Módulo 1 - Fundamentos"
                                            value="<?php echo htmlspecialchars($moduloEditando['titulo'] ?? ''); ?>"
                                            required
                                        >
                                    </div>

                                    <div class="form-group">
                                        <label for="ordem">Ordem</label>
                                        <input 
                                            type="number" 
                                            id="ordem" 
                                            name="ordem" 
                                            min="1"
                                            value="<?php echo htmlspecialchars($moduloEditando['ordem'] ?? 1); ?>"
                                        >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="descricao">Descrição</label>
                                    <textarea 
                                        id="descricao" 
                                        name="descricao" 
                                        placeholder="Descrição do módulo..."
                                        rows="3"
                                    ><?php echo htmlspecialchars($moduloEditando['descricao'] ?? ''); ?></textarea>
                                </div>

                                <div style="display: flex; gap: 10px;">
                                    <button type="submit" class="btn btn-primary">
                                        <?php echo $moduloEditando ? '💾 Atualizar' : '➕ Criar Módulo'; ?>
                                    </button>
                                    <?php if ($moduloEditando): ?>
                                        <a href="/admin/modules.php?curso_id=<?php echo $cursoId; ?>" class="btn btn-secondary">Cancelar</a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Lista de Módulos -->
                    <div class="card">
                        <div class="card-header">
                            Módulos (<?php echo count($modulos); ?>)
                        </div>
                        <div class="card-body">
                            <?php if (count($modulos) > 0): ?>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Ordem</th>
                                            <th>Título</th>
                                            <th>Aulas</th>
                                            <th>Criado em</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($modulos as $mod): 
                                            $countAulas = getRow($conn, 'SELECT COUNT(*) as total FROM aulas WHERE modulo_id = ?', [$mod['id']]);
                                        ?>
                                            <tr>
                                                <td>
                                                    <span class="badge badge-info">#<?php echo $mod['ordem']; ?></span>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($mod['titulo']); ?></strong><br>
                                                    <small style="color: #7f8c8d;">
                                                        <?php echo htmlspecialchars(substr($mod['descricao'], 0, 50)); ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-success">
                                                        <?php echo $countAulas['total']; ?> aula(s)
                                                    </span>
                                                </td>
                                                <td style="font-size: 12px;">
                                                    <?php echo (new DateTime($mod['created_at']))->format('d/m/Y'); ?>
                                                </td>
                                                <td>
                                                    <div class="table-actions">
                                                        <a href="/admin/lessons.php?modulo_id=<?php echo $mod['id']; ?>" class="btn btn-secondary btn-small">
                                                            📝 Aulas
                                                        </a>
                                                        <a href="?curso_id=<?php echo $cursoId; ?>&editar=<?php echo $mod['id']; ?>" class="btn btn-warning btn-small">
                                                            ✏️ Editar
                                                        </a>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="acao" value="deletar">
                                                            <input type="hidden" name="curso_id" value="<?php echo $cursoId; ?>">
                                                            <input type="hidden" name="modulo_id" value="<?php echo $mod['id']; ?>">
                                                            <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('Deletar módulo e suas aulas?')">
                                                                🗑️ Deletar
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-muted text-center" style="padding: 40px 0;">
                                    Nenhum módulo criado neste curso. Crie o primeiro acima!
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        ℹ️ Crie um curso primeiro em "Gerenciar Cursos" para adicionar módulos.
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background-color: #2c3e50; color: white; text-align: center; padding: 20px; margin-top: 50px;">
        <p>&copy; 2024 NR1 EAD. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
