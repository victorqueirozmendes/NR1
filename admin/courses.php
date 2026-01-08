<?php
/**
 * Admin - Gerenciamento de Cursos
 * /admin/courses.php
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
    
    if ($acao === 'criar') {
        $titulo = $_POST['titulo'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $instrutor = $_POST['instrutor'] ?? '';
        
        if (empty($titulo) || empty($descricao)) {
            $erro = 'Título e descrição são obrigatórios!';
        } else {
            executeQuery($conn,
                'INSERT INTO cursos (titulo, descricao, instrutor, created_at) VALUES (?, ?, ?, NOW())',
                [$titulo, $descricao, $instrutor]
            );
            $mensagem = 'Curso criado com sucesso!';
        }
    } elseif ($acao === 'editar') {
        $cursoId = $_POST['curso_id'] ?? '';
        $titulo = $_POST['titulo'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $instrutor = $_POST['instrutor'] ?? '';
        
        if (empty($titulo)) {
            $erro = 'Título é obrigatório!';
        } else {
            executeQuery($conn,
                'UPDATE cursos SET titulo = ?, descricao = ?, instrutor = ? WHERE id = ?',
                [$titulo, $descricao, $instrutor, $cursoId]
            );
            $mensagem = 'Curso atualizado com sucesso!';
        }
    } elseif ($acao === 'deletar') {
        $cursoId = $_POST['curso_id'] ?? '';
        executeQuery($conn, 'DELETE FROM cursos WHERE id = ?', [$cursoId]);
        $mensagem = 'Curso deletado com sucesso!';
    }
}

// Buscar cursos
$cursos = getRows($conn, 'SELECT * FROM cursos ORDER BY created_at DESC', []);

// Verificar qual curso está sendo editado
$cursoEditando = null;
if (isset($_GET['editar'])) {
    $cursoEditando = getRow($conn, 'SELECT * FROM cursos WHERE id = ?', [$_GET['editar']]);
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="Gerenciar cursos - NR1 EAD">
    <meta name="theme-color" content="#3498db">
    <title>Gerenciar Cursos - NR1 EAD</title>
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
                    <li><a href="/admin/courses.php" class="active">📚 Cursos</a></li>
                    <li><a href="/admin/modules.php">📋 Módulos</a></li>
                    <li><a href="/admin/lessons.php">📝 Aulas</a></li>
                    <li><a href="/admin/material-upload.php">📎 Materiais</a></li>
                </ul>
            </aside>

            <!-- Conteúdo Principal -->
            <main class="main-content">
                <h1>📚 Gerenciar Cursos</h1>
                
                <!-- Alertas -->
                <?php if ($mensagem): ?>
                    <div class="alert alert-success">✓ <?php echo htmlspecialchars($mensagem); ?></div>
                <?php endif; ?>
                
                <?php if ($erro): ?>
                    <div class="alert alert-danger">✗ <?php echo htmlspecialchars($erro); ?></div>
                <?php endif; ?>

                <!-- Formulário Criar/Editar -->
                <div class="card mb-30">
                    <div class="card-header">
                        <?php echo $cursoEditando ? '✏️ Editar Curso' : '➕ Novo Curso'; ?>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="form">
                            <input type="hidden" name="acao" value="<?php echo $cursoEditando ? 'editar' : 'criar'; ?>">
                            <?php if ($cursoEditando): ?>
                                <input type="hidden" name="curso_id" value="<?php echo htmlspecialchars($cursoEditando['id']); ?>">
                            <?php endif; ?>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="titulo">Título do Curso *</label>
                                    <input 
                                        type="text" 
                                        id="titulo" 
                                        name="titulo" 
                                        placeholder="Ex: Introdução ao PHP"
                                        value="<?php echo htmlspecialchars($cursoEditando['titulo'] ?? ''); ?>"
                                        required
                                    >
                                </div>

                                <div class="form-group">
                                    <label for="instrutor">Instrutor</label>
                                    <input 
                                        type="text" 
                                        id="instrutor" 
                                        name="instrutor" 
                                        placeholder="Nome do instrutor"
                                        value="<?php echo htmlspecialchars($cursoEditando['instrutor'] ?? ''); ?>"
                                    >
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="descricao">Descrição *</label>
                                <textarea 
                                    id="descricao" 
                                    name="descricao" 
                                    placeholder="Descrição do curso..."
                                    rows="4"
                                    required
                                ><?php echo htmlspecialchars($cursoEditando['descricao'] ?? ''); ?></textarea>
                            </div>

                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <?php echo $cursoEditando ? '💾 Atualizar' : '➕ Criar Curso'; ?>
                                </button>
                                <?php if ($cursoEditando): ?>
                                    <a href="/admin/courses.php" class="btn btn-secondary">Cancelar</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista de Cursos -->
                <div class="card">
                    <div class="card-header">
                        Cursos (<?php echo count($cursos); ?>)
                    </div>
                    <div class="card-body">
                        <?php if (count($cursos) > 0): ?>
                            <div class="courses-grid">
                                <?php foreach ($cursos as $curso): ?>
                                    <div class="course-card">
                                        <div class="course-header">
                                            <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                                            <span class="badge badge-info"><?php echo $curso['id']; ?></span>
                                        </div>
                                        
                                        <p class="course-description">
                                            <?php echo htmlspecialchars(substr($curso['descricao'], 0, 100)); ?>...
                                        </p>

                                        <?php if ($curso['instrutor']): ?>
                                            <p class="course-meta">
                                                👨‍🏫 <strong><?php echo htmlspecialchars($curso['instrutor']); ?></strong>
                                            </p>
                                        <?php endif; ?>

                                        <p class="course-meta">
                                            📅 <?php echo (new DateTime($curso['created_at']))->format('d/m/Y'); ?>
                                        </p>

                                        <div class="course-actions">
                                            <a href="/admin/modules.php?curso_id=<?php echo $curso['id']; ?>" class="btn btn-secondary btn-small">
                                                📋 Módulos
                                            </a>
                                            <a href="?editar=<?php echo $curso['id']; ?>" class="btn btn-warning btn-small">
                                                ✏️ Editar
                                            </a>
                                            <form method="POST">
                                                <input type="hidden" name="acao" value="deletar">
                                                <input type="hidden" name="curso_id" value="<?php echo $curso['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('Deletar? Seus módulos também serão deletados!')">
                                                    🗑️ Deletar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center">
                                Nenhum curso criado ainda. Crie o primeiro acima!
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 NR1 EAD. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
