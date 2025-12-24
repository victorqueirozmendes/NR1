<?php
/**
 * Admin - Gerenciamento de Aulas
 * /admin/lessons.php
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

verificarAdmin();

$usuario = getUsuarioLogado();
$mensagem = '';
$erro = '';

// Buscar módulo selecionado
$moduloId = $_GET['modulo_id'] ?? $_POST['modulo_id'] ?? '';
$moduloAtual = null;

if ($moduloId) {
    $moduloAtual = getRow($conn, 'SELECT * FROM modulos WHERE id = ?', [$moduloId]);
} else {
    $moduloAtual = getRow($conn, 'SELECT * FROM modulos LIMIT 1', []);
    if ($moduloAtual) {
        $moduloId = $moduloAtual['id'];
    }
}

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'criar') {
        $titulo = $_POST['titulo'] ?? '';
        $conteudo = $_POST['conteudo'] ?? '';
        $ordem = $_POST['ordem'] ?? 1;
        
        if (empty($titulo)) {
            $erro = 'Título é obrigatório!';
        } else {
            executeQuery($conn,
                'INSERT INTO aulas (modulo_id, titulo, conteudo, ordem, created_at) VALUES (?, ?, ?, ?, NOW())',
                [$moduloId, $titulo, $conteudo, $ordem]
            );
            $mensagem = 'Aula criada com sucesso!';
        }
    } elseif ($acao === 'editar') {
        $aulaId = $_POST['aula_id'] ?? '';
        $titulo = $_POST['titulo'] ?? '';
        $conteudo = $_POST['conteudo'] ?? '';
        $ordem = $_POST['ordem'] ?? 1;
        
        if (empty($titulo)) {
            $erro = 'Título é obrigatório!';
        } else {
            executeQuery($conn,
                'UPDATE aulas SET titulo = ?, conteudo = ?, ordem = ? WHERE id = ?',
                [$titulo, $conteudo, $ordem, $aulaId]
            );
            $mensagem = 'Aula atualizada!';
        }
    } elseif ($acao === 'deletar') {
        $aulaId = $_POST['aula_id'] ?? '';
        executeQuery($conn, 'DELETE FROM aulas WHERE id = ?', [$aulaId]);
        $mensagem = 'Aula deletada!';
    }
}

// Buscar aulas do módulo
$aulas = [];
if ($moduloId) {
    $aulas = getRows($conn, 
        'SELECT * FROM aulas WHERE modulo_id = ? ORDER BY ordem ASC',
        [$moduloId]
    );
}

// Verificar qual aula está sendo editada
$aulaEditando = null;
if (isset($_GET['editar'])) {
    $aulaEditando = getRow($conn, 'SELECT * FROM aulas WHERE id = ?', [$_GET['editar']]);
}

// Buscar todos os módulos para seletor
$todosOsModulos = getRows($conn, 
    'SELECT m.id, m.titulo, c.titulo as curso_titulo FROM modulos m 
     JOIN cursos c ON m.curso_id = c.id 
     ORDER BY c.titulo, m.ordem',
    []
);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="Gerenciar aulas e conteúdo - NR1 EAD">
    <meta name="theme-color" content="#3498db">
    <title>Gerenciar Aulas - NR1 EAD</title>
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
                    <li><a href="/admin/modules.php">📋 Módulos</a></li>
                    <li><a href="/admin/lessons.php" class="active">📝 Aulas</a></li>
                    <li><a href="/admin/material-upload.php">📎 Materiais</a></li>
                </ul>
            </sidebar>

            <!-- Conteúdo Principal -->
            <main class="main-content">
                <h1>📝 Gerenciar Aulas</h1>
                
                <!-- Alertas -->
                <?php if ($mensagem): ?>
                    <div class="alert alert-success">✓ <?php echo htmlspecialchars($mensagem); ?></div>
                <?php endif; ?>
                
                <?php if ($erro): ?>
                    <div class="alert alert-danger">✗ <?php echo htmlspecialchars($erro); ?></div>
                <?php endif; ?>

                <!-- Seletor de Módulo -->
                <div class="card mb-30">
                    <div class="card-header">Selecionar Módulo</div>
                    <div class="card-body">
                        <form method="GET" style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <select name="modulo_id" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid #bdc3c7; border-radius: 4px; flex: 1; min-width: 200px;">
                                <option value="">-- Selecione um módulo --</option>
                                <?php foreach ($todosOsModulos as $m): ?>
                                    <option value="<?php echo $m['id']; ?>" <?php echo $moduloId == $m['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($m['curso_titulo'] . ' > ' . $m['titulo']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                </div>

                <?php if ($moduloAtual): ?>
                    <!-- Formulário Criar/Editar Aula -->
                    <div class="card mb-30">
                        <div class="card-header">
                            <?php echo $aulaEditando ? '✏️ Editar Aula' : '➕ Nova Aula'; ?>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="form">
                                <input type="hidden" name="acao" value="<?php echo $aulaEditando ? 'editar' : 'criar'; ?>">
                                <input type="hidden" name="modulo_id" value="<?php echo $moduloId; ?>">
                                <?php if ($aulaEditando): ?>
                                    <input type="hidden" name="aula_id" value="<?php echo htmlspecialchars($aulaEditando['id']); ?>">
                                <?php endif; ?>

                                <div class="form-group">
                                    <label>Módulo: <strong><?php echo htmlspecialchars($moduloAtual['titulo']); ?></strong></label>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 100px; gap: 15px;">
                                    <div class="form-group">
                                        <label for="titulo">Título da Aula *</label>
                                        <input 
                                            type="text" 
                                            id="titulo" 
                                            name="titulo" 
                                            placeholder="Ex: Aula 1 - Introdução"
                                            value="<?php echo htmlspecialchars($aulaEditando['titulo'] ?? ''); ?>"
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
                                            value="<?php echo htmlspecialchars($aulaEditando['ordem'] ?? 1); ?>"
                                        >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="conteudo">Conteúdo (HTML permitido) *</label>
                                    <textarea 
                                        id="conteudo" 
                                        name="conteudo" 
                                        placeholder="Conteúdo da aula..."
                                        rows="8"
                                        required
                                    ><?php echo htmlspecialchars($aulaEditando['conteudo'] ?? ''); ?></textarea>
                                    <small style="color: #7f8c8d;">
                                        Você pode usar HTML básico: &lt;p&gt;, &lt;h2&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;a&gt;, etc.
                                    </small>
                                </div>

                                <div style="display: flex; gap: 10px;">
                                    <button type="submit" class="btn btn-primary">
                                        <?php echo $aulaEditando ? '💾 Atualizar' : '➕ Criar Aula'; ?>
                                    </button>
                                    <?php if ($aulaEditando): ?>
                                        <a href="/admin/lessons.php?modulo_id=<?php echo $moduloId; ?>" class="btn btn-secondary">Cancelar</a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Lista de Aulas -->
                    <div class="card">
                        <div class="card-header">
                            Aulas (<?php echo count($aulas); ?>)
                        </div>
                        <div class="card-body">
                            <?php if (count($aulas) > 0): ?>
                                <div class="lessons-list">
                                    <?php foreach ($aulas as $aula): ?>
                                        <div class="lesson-item">
                                            <div class="lesson-header">
                                                <div>
                                                    <span class="lesson-number">Aula <?php echo $aula['ordem']; ?></span>
                                                    <h3><?php echo htmlspecialchars($aula['titulo']); ?></h3>
                                                </div>
                                                <span class="badge badge-info">
                                                    📅 <?php echo (new DateTime($aula['created_at']))->format('d/m/Y'); ?>
                                                </span>
                                            </div>
                                            
                                            <p class="lesson-preview">
                                                <?php echo htmlspecialchars(substr(strip_tags($aula['conteudo']), 0, 100)); ?>...
                                            </p>

                                            <div class="lesson-actions">
                                                <a href="?modulo_id=<?php echo $moduloId; ?>&editar=<?php echo $aula['id']; ?>" class="btn btn-warning btn-small">
                                                    ✏️ Editar
                                                </a>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="acao" value="deletar">
                                                    <input type="hidden" name="modulo_id" value="<?php echo $moduloId; ?>">
                                                    <input type="hidden" name="aula_id" value="<?php echo $aula['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('Deletar aula?')">
                                                        🗑️ Deletar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center" style="padding: 40px 0;">
                                    Nenhuma aula criada neste módulo. Crie a primeira acima!
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        ℹ️ Crie um módulo primeiro em "Gerenciar Módulos" para adicionar aulas.
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background-color: #2c3e50; color: white; text-align: center; padding: 20px; margin-top: 50px;">
        <p>&copy; 2024 NR1 EAD. Todos os direitos reservados.</p>
    </footer>

    <style>
        .lessons-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .lesson-item {
            border: 1px solid #ecf0f1;
            border-radius: 4px;
            padding: 15px;
            background: white;
            transition: all 0.3s;
        }

        .lesson-item:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-color: #3498db;
        }

        .lesson-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .lesson-number {
            color: #3498db;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .lesson-header h3 {
            color: #2c3e50;
            font-size: 16px;
            margin: 5px 0 0 0;
        }

        .lesson-preview {
            color: #7f8c8d;
            font-size: 13px;
            line-height: 1.4;
            margin: 8px 0;
        }

        .lesson-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
    </style>
</body>
</html>
