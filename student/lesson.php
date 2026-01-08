<?php
/**
 * Visualização de Aula com Progresso e Comentários
 * /student/lesson.php
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

verificarLogin();

$usuario = getUsuarioLogado();

if ($usuario['role'] === 'admin') {
    header('Location: /admin/users.php');
    exit;
}

// Buscar aula
$aulaId = $_GET['id'] ?? '';
$aula = getRow($conn, 'SELECT * FROM aulas WHERE id = ?', [$aulaId]);

if (!$aula) {
    header('Location: /student/dashboard.php');
    exit;
}

// Buscar módulo e curso para verificar acesso
$modulo = getRow($conn, 'SELECT * FROM modulos WHERE id = ?', [$aula['modulo_id']]);
$curso = getRow($conn, 'SELECT * FROM cursos WHERE id = ?', [$modulo['curso_id']]);

// Verificar se aluno está inscrito no curso
$acesso = getRow($conn,
    'SELECT id FROM acessos WHERE usuario_id = ? AND curso_id = ?',
    [$usuario['id'], $modulo['curso_id']]
);

if (!$acesso) {
    header('Location: /student/dashboard.php');
    exit;
}

$mensagem = '';
$erro = '';

// Processar marcação como completa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'marcar_completa') {
        // Verificar se já existe progresso
        $progresso = getRow($conn,
            'SELECT id FROM progresso WHERE usuario_id = ? AND aula_id = ?',
            [$usuario['id'], $aulaId]
        );
        
        if ($progresso) {
            // Atualizar
            executeQuery($conn,
                'UPDATE progresso SET completado = 1, data_conclusao = NOW() WHERE usuario_id = ? AND aula_id = ?',
                [$usuario['id'], $aulaId]
            );
        } else {
            // Criar
            executeQuery($conn,
                'INSERT INTO progresso (usuario_id, aula_id, completado, data_conclusao) VALUES (?, ?, 1, NOW())',
                [$usuario['id'], $aulaId]
            );
        }
        $mensagem = '✓ Aula marcada como completa!';
    } elseif ($acao === 'comentar') {
        $comentario = $_POST['comentario'] ?? '';
        
        if (empty($comentario)) {
            $erro = 'Escreva um comentário!';
        } else {
            executeQuery($conn,
                'INSERT INTO comentarios (usuario_id, aula_id, comentario, created_at) VALUES (?, ?, ?, NOW())',
                [$usuario['id'], $aulaId, $comentario]
            );
            $mensagem = 'Comentário enviado com sucesso!';
        }
    }
}

// Buscar progresso da aula
$progresso = getRow($conn,
    'SELECT * FROM progresso WHERE usuario_id = ? AND aula_id = ?',
    [$usuario['id'], $aulaId]
);

$estaCompletada = $progresso && $progresso['completado'] == 1;

// Buscar materiais da aula
$materiais = getRows($conn,
    'SELECT * FROM materiais WHERE aula_id = ? ORDER BY created_at DESC',
    [$aulaId]
);

// Buscar comentários
$comentarios = getRows($conn,
    'SELECT c.*, u.nome FROM comentarios c
     JOIN usuarios u ON c.usuario_id = u.id
     WHERE c.aula_id = ? AND c.oculto = 0
     ORDER BY c.created_at DESC',
    [$aulaId]
);

// Buscar aulas anteriores e próximas
$aulaAnterior = getRow($conn,
    'SELECT id, titulo FROM aulas 
     WHERE modulo_id = ? AND ordem < ?
     ORDER BY ordem DESC LIMIT 1',
    [$aula['modulo_id'], $aula['ordem']]
);

$aulaProxima = getRow($conn,
    'SELECT id, titulo FROM aulas
     WHERE modulo_id = ? AND ordem > ?
     ORDER BY ordem ASC LIMIT 1',
    [$aula['modulo_id'], $aula['ordem']]
);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="<?php echo htmlspecialchars($aula['titulo']); ?> - Aula da plataforma NR1 EAD">
    <meta name="theme-color" content="#3498db">
    <title><?php echo htmlspecialchars($aula['titulo']); ?> - NR1 EAD</title>
    <link rel="stylesheet" href="/css/style-mobile-first.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a href="/" class="navbar-brand">🎓 NR1 EAD</a>
        <div class="navbar-menu">
            <span>Olá, <strong><?php echo htmlspecialchars($usuario['nome']); ?></strong></span>
            <a href="/logout.php">Sair</a>
        </div>
    </nav>

    <!-- Container Principal -->
    <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <a href="/student/dashboard.php">Dashboard</a>
            <span>/</span>
            <a href="/student/course.php?id=<?php echo $modulo['curso_id']; ?>">
                <?php echo htmlspecialchars($curso['titulo']); ?>
            </a>
            <span>/</span>
            <a href="/student/course.php?id=<?php echo $modulo['curso_id']; ?>#modulo-<?php echo $modulo['id']; ?>">
                <?php echo htmlspecialchars($modulo['titulo']); ?>
            </a>
            <span>/</span>
            <span><?php echo htmlspecialchars($aula['titulo']); ?></span>
        </nav>

        <div class="lesson-container">
            <!-- Conteúdo Principal -->
            <main class="lesson-content">
                <h1><?php echo htmlspecialchars($aula['titulo']); ?></h1>

                <!-- Alertas -->
                <?php if ($mensagem): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($mensagem); ?>
                    </div>
                <?php endif; ?>

                <?php if ($erro): ?>
                    <div class="alert alert-danger">
                        ✗ <?php echo htmlspecialchars($erro); ?>
                    </div>
                <?php endif; ?>

                <!-- Status da Aula -->
                <?php if ($estaCompletada): ?>
                    <div class="alert alert-success">
                        ✓ Você já completou esta aula em <?php echo (new DateTime($progresso['data_conclusao']))->format('d/m/Y H:i'); ?>
                    </div>
                <?php endif; ?>

                <!-- Vídeo do YouTube -->
                <?php if (!empty($aula['youtube_url'])): ?>
                    <div class="video-container">
                        <?php 
                        // Extrair ID do vídeo da URL
                        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $aula['youtube_url'], $matches);
                        $videoId = $matches[1] ?? '';
                        
                        if ($videoId):
                        ?>
                            <iframe 
                                width="100%" 
                                height="400" 
                                src="https://www.youtube.com/embed/<?php echo htmlspecialchars($videoId); ?>" 
                                title="Vídeo da Aula"
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                allowfullscreen>
                            </iframe>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Conteúdo da Aula -->
                <div class="lesson-content-body">
                    <?php echo $aula['conteudo']; ?>
                </div>

                <!-- Materiais -->
                <?php if (count($materiais) > 0): ?>
                    <div class="card">
                        <div class="card-header">📎 Materiais para Download</div>
                        <div class="card-body">
                            <div class="materials-list">
                                <?php foreach ($materiais as $material): ?>
                                    <a href="/uploads/materiais/<?php echo htmlspecialchars($material['arquivo']); ?>" 
                                       target="_blank" 
                                       class="material-item">
                                        <span class="material-icon">📄</span>
                                        <div class="material-info">
                                            <div class="material-name"><?php echo htmlspecialchars($material['titulo']); ?></div>
                                            <div class="material-date">
                                                <?php echo (new DateTime($material['created_at']))->format('d/m/Y'); ?>
                                            </div>
                                        </div>
                                        <span class="material-download">→</span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Botão Marcar Como Completa -->
                <?php if (!$estaCompletada): ?>
                    <form method="POST" class="form">
                        <input type="hidden" name="acao" value="marcar_completa">
                        <button type="submit" class="btn btn-success btn-large">
                            ✓ Marcar Como Completa
                        </button>
                    </form>
                <?php endif; ?>

                <!-- Navegação Entre Aulas -->
                <div class="lesson-nav">
                    <?php if ($aulaAnterior): ?>
                        <a href="/student/lesson.php?id=<?php echo $aulaAnterior['id']; ?>" class="btn btn-secondary">
                            ← Anterior
                        </a>
                    <?php else: ?>
                        <div></div>
                    <?php endif; ?>

                    <?php if ($aulaProxima): ?>
                        <a href="/student/lesson.php?id=<?php echo $aulaProxima['id']; ?>" class="btn btn-primary">
                            Próxima →
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Comentários -->
                <div class="card">
                    <div class="card-header">💬 Comentários (<?php echo count($comentarios); ?>)</div>
                    <div class="card-body">
                        <!-- Formulário de Comentário -->
                        <form method="POST" class="form comment-form">
                            <input type="hidden" name="acao" value="comentar">
                            
                            <div class="form-group">
                                <textarea 
                                    name="comentario" 
                                    placeholder="Deixe seu comentário ou dúvida..."
                                    rows="3"
                                    required
                                ></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">
                                📤 Enviar
                            </button>
                        </form>

                        <!-- Lista de Comentários -->
                        <?php if (count($comentarios) > 0): ?>
                            <div class="comments-list">
                                <?php foreach ($comentarios as $com): ?>
                                    <div class="comment-item">
                                        <div class="comment-author"><?php echo htmlspecialchars($com['nome']); ?></div>
                                        <div class="comment-date">
                                            <?php echo (new DateTime($com['created_at']))->format('d/m/Y H:i'); ?>
                                        </div>
                                        <p class="comment-text">
                                            <?php echo nl2br(htmlspecialchars($com['comentario'])); ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-center text-muted">
                                Nenhum comentário ainda. Seja o primeiro!
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
