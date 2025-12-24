<?php
/**
 * Detalhes do Curso
 * /student/course.php
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

verificarLogin();

$usuario = getUsuarioLogado();

if ($usuario['role'] === 'admin') {
    header('Location: /admin/users.php');
    exit;
}

// Buscar curso
$cursoId = $_GET['id'] ?? '';
$curso = getRow($conn, 'SELECT * FROM cursos WHERE id = ?', [$cursoId]);

if (!$curso) {
    header('Location: /student/courses.php');
    exit;
}

// Verificar se aluno está inscrito
$acesso = getRow($conn,
    'SELECT id FROM acessos WHERE usuario_id = ? AND curso_id = ?',
    [$usuario['id'], $cursoId]
);

if (!$acesso) {
    header('Location: /student/courses.php');
    exit;
}

// Buscar módulos e aulas do curso
$modulos = getRows($conn,
    'SELECT * FROM modulos WHERE curso_id = ? ORDER BY ordem ASC',
    [$cursoId]
);

// Buscar progresso geral do curso
$totalAulas = getRow($conn,
    'SELECT COUNT(au.id) as total FROM aulas au
     INNER JOIN modulos m ON au.modulo_id = m.id
     WHERE m.curso_id = ?',
    [$cursoId]
);

$aulasCompletadas = getRow($conn,
    'SELECT COUNT(p.id) as total FROM progresso p
     INNER JOIN aulas au ON p.aula_id = au.id
     INNER JOIN modulos m ON au.modulo_id = m.id
     WHERE m.curso_id = ? AND p.usuario_id = ? AND p.completado = 1',
    [$cursoId, $usuario['id']]
);

$progressoPercentual = $totalAulas['total'] > 0
    ? round(($aulasCompletadas['total'] / $totalAulas['total']) * 100)
    : 0;

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="<?php echo htmlspecialchars($curso['titulo']); ?> - Curso da plataforma NR1 EAD">
    <meta name="theme-color" content="#3498db">
    <title><?php echo htmlspecialchars($curso['titulo']); ?> - NR1 EAD</title>
    <link rel="stylesheet" href="/css/style-mobile-first.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a href="/" class="navbar-brand">🎓 NR1 EAD</a>
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
                <h3>📚 MEUS CURSOS</h3>
                <ul class="sidebar-menu">
                    <li><a href="/student/dashboard.php">📊 Dashboard</a></li>
                    <li><a href="/student/courses.php">📚 Explorar Cursos</a></li>
                    <li>
                        <h4>MÓDULOS</h4>
                    </li>
                    <?php foreach ($modulos as $mod): ?>
                        <li>
                            <a href="#modulo-<?php echo $mod['id']; ?>">
                                📋 <?php echo htmlspecialchars(substr($mod['titulo'], 0, 25)); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>

            <!-- Conteúdo Principal -->
            <main class="main-content">
                <!-- Header do Curso -->
                <div class="course-header-detail">
                    <h1><?php echo htmlspecialchars($curso['titulo']); ?></h1>
                    <p>
                        <?php echo htmlspecialchars($curso['descricao']); ?>
                    </p>
                    
                    <div>
                        <div class="stat-box">
                            <div class="stat-number"><?php echo count($modulos); ?></div>
                            <div class="stat-label">Módulos</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number"><?php echo $totalAulas['total']; ?></div>
                            <div class="stat-label">Aulas</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number"><?php echo $progressoPercentual; ?>%</div>
                            <div class="stat-label">Progresso</div>
                        </div>
                    </div>

                    <!-- Barra de Progresso -->
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                </div>

                <!-- Módulos e Aulas -->
                <?php foreach ($modulos as $modulo):
                    $aulas = getRows($conn,
                        'SELECT * FROM aulas WHERE modulo_id = ? ORDER BY ordem ASC',
                        [$modulo['id']]
                    );
                ?>
                    <div class="card mb-30" id="modulo-<?php echo $modulo['id']; ?>">
                        <div class="card-header">
                            📋 <?php echo htmlspecialchars($modulo['titulo']); ?>
                            <span>
                                <?php echo count($aulas); ?> aula(s)
                            </span>
                        </div>
                        
                        <?php if (!empty($modulo['descricao'])): ?>
                            <div class="card-body">
                                <p>
                                    <?php echo htmlspecialchars($modulo['descricao']); ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <div class="card-body">
                            <?php if (count($aulas) > 0): ?>
                                <ul class="lessons-list-course">
                                    <?php foreach ($aulas as $aula):
                                        $progresso = getRow($conn,
                                            'SELECT completado FROM progresso WHERE usuario_id = ? AND aula_id = ?',
                                            [$usuario['id'], $aula['id']]
                                        );
                                        $estaCompletada = $progresso && $progresso['completado'] == 1;
                                    ?>
                                        <li class="lesson-item-course <?php echo $estaCompletada ? 'completed' : ''; ?>">
                                            <div class="lesson-checkbox">
                                                <?php if ($estaCompletada): ?>
                                                    <span class="checkbox-icon completed">✓</span>
                                                <?php else: ?>
                                                    <span class="checkbox-icon">◯</span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="lesson-info">
                                                <h4><?php echo htmlspecialchars($aula['titulo']); ?></h4>
                                                <p><?php echo htmlspecialchars(substr(strip_tags($aula['conteudo']), 0, 80)); ?>...</p>
                                            </div>

                                            <a href="/student/lesson.php?id=<?php echo $aula['id']; ?>" class="btn btn-small btn-primary">
                                                Assistir
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted">Nenhuma aula neste módulo ainda.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 NR1 EAD. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
