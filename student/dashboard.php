<?php
/**
 * Dashboard do Aluno
 * /student/dashboard.php
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

// Verificar acesso - apenas alunos logados
verificarLogin();

$usuario = getUsuarioLogado();

// Se for admin, redirecionar para admin dashboard
if ($usuario['role'] === 'admin') {
    header('Location: /admin/users.php');
    exit;
}

// Buscar cursos disponíveis e inscritos
$cursosInscritos = getRows($conn,
    'SELECT DISTINCT c.* FROM cursos c
     INNER JOIN acessos a ON c.id = a.curso_id
     WHERE a.usuario_id = ?
     ORDER BY c.created_at DESC',
    [$usuario['id']]
);

$cursosDisponiveis = getRows($conn,
    'SELECT c.* FROM cursos c
     WHERE c.id NOT IN (
        SELECT DISTINCT curso_id FROM acessos WHERE usuario_id = ?
     )
     ORDER BY c.created_at DESC',
    [$usuario['id']]
);

// Buscar progresso geral
$totalModulos = getRow($conn,
    'SELECT COUNT(DISTINCT m.id) as total FROM modulos m
     INNER JOIN cursos c ON m.curso_id = c.id
     INNER JOIN acessos a ON c.id = a.curso_id
     WHERE a.usuario_id = ?',
    [$usuario['id']]
);

$totalAulas = getRow($conn,
    'SELECT COUNT(DISTINCT au.id) as total FROM aulas au
     INNER JOIN modulos m ON au.modulo_id = m.id
     INNER JOIN cursos c ON m.curso_id = c.id
     INNER JOIN acessos a ON c.id = a.curso_id
     WHERE a.usuario_id = ?',
    [$usuario['id']]
);

$aulasCompletadas = getRow($conn,
    'SELECT COUNT(*) as total FROM progresso
     WHERE usuario_id = ? AND completado = 1',
    [$usuario['id']]
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
    <meta name="description" content="Dashboard do aluno - NR1 EAD">
    <meta name="theme-color" content="#3498db">
    <title>Dashboard - NR1 EAD</title>
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
                    <li><a href="/student/dashboard.php" class="active">📊 Dashboard</a></li>
                    <li><a href="/student/courses.php">📚 Explorar Cursos</a></li>
                    <li><a href="/">🏠 Voltar ao Site</a></li>
                </ul>
            </aside>

            <!-- Conteúdo Principal -->
            <main class="main-content">
                <h1>Bem-vindo, <?php echo htmlspecialchars($usuario['nome']); ?>!</h1>

                <!-- Progresso Geral -->
                <div>
                    <div class="stat-box">
                        <div class="stat-number"><?php echo count($cursosInscritos); ?></div>
                        <div class="stat-label">Cursos Inscritos</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number"><?php echo $totalAulas['total']; ?></div>
                        <div class="stat-label">Aulas Disponíveis</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number"><?php echo $aulasCompletadas['total']; ?></div>
                        <div class="stat-label">Aulas Concluídas</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number"><?php echo $progressoPercentual; ?>%</div>
                        <div class="stat-label">Progresso Geral</div>
                    </div>
                </div>

                <!-- Barra de Progresso -->
                <div class="card mb-30">
                    <div class="card-header">📊 Seu Progresso Geral</div>
                    <div class="card-body">
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                        <p>
                            Você completou <strong><?php echo $aulasCompletadas['total']; ?> de <?php echo $totalAulas['total']; ?></strong> aulas
                        </p>
                    </div>
                </div>

                <!-- Cursos Inscritos -->
                <div class="card mb-30">
                    <div class="card-header">
                        📚 Meus Cursos (<?php echo count($cursosInscritos); ?>)
                    </div>
                    <div class="card-body">
                        <?php if (count($cursosInscritos) > 0): ?>
                            <div class="courses-grid">
                                <?php foreach ($cursosInscritos as $curso):
                                    // Calcular progresso do curso
                                    $totalAulasCurso = getRow($conn,
                                        'SELECT COUNT(au.id) as total FROM aulas au
                                         INNER JOIN modulos m ON au.modulo_id = m.id
                                         WHERE m.curso_id = ?',
                                        [$curso['id']]
                                    );
                                    
                                    $aulasConcluidasCurso = getRow($conn,
                                        'SELECT COUNT(p.id) as total FROM progresso p
                                         INNER JOIN aulas au ON p.aula_id = au.id
                                         INNER JOIN modulos m ON au.modulo_id = m.id
                                         WHERE m.curso_id = ? AND p.usuario_id = ? AND p.completado = 1',
                                        [$curso['id'], $usuario['id']]
                                    );

                                    $progressoCurso = $totalAulasCurso['total'] > 0 
                                        ? round(($aulasConcluidasCurso['total'] / $totalAulasCurso['total']) * 100) 
                                        : 0;
                                ?>
                                    <div class="course-card">
                                        <div class="course-header">
                                            <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                                        </div>
                                        
                                        <p class="course-description">
                                            <?php echo htmlspecialchars(substr($curso['descricao'], 0, 80)); ?>...
                                        </p>

                                        <div class="course-progress">
                                            <div class="progress-bar-small">
                                                <div class="progress-fill"></div>
                                            </div>
                                            <small>
                                                <?php echo $aulasConcluidasCurso['total']; ?>/<?php echo $totalAulasCurso['total']; ?> aulas
                                            </small>
                                        </div>

                                        <div class="course-actions">
                                            <a href="/student/course.php?id=<?php echo $curso['id']; ?>" class="btn btn-primary btn-block">
                                                Continuar Estudando
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center">
                                Você ainda não está inscrito em nenhum curso.<br>
                                <a href="/student/courses.php">
                                    Explore cursos disponíveis →
                                </a>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Cursos Disponíveis -->
                <?php if (count($cursosDisponiveis) > 0): ?>
                    <div class="card">
                        <div class="card-header">
                            ✨ Cursos Disponíveis para Inscrição (<?php echo count($cursosDisponiveis); ?>)
                        </div>
                        <div class="card-body">
                            <div class="courses-grid-small">
                                <?php foreach (array_slice($cursosDisponiveis, 0, 3) as $curso): ?>
                                    <div class="course-card-small">
                                        <h4><?php echo htmlspecialchars($curso['titulo']); ?></h4>
                                        <p><?php echo htmlspecialchars(substr($curso['descricao'], 0, 60)); ?>...</p>
                                        <a href="/student/courses.php" class="btn btn-secondary btn-small">
                                            Ver Mais
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($cursosDisponiveis) > 3): ?>
                                <p>
                                    <a href="/student/courses.php" class="text-primary">
                                        Ver todos os <?php echo count($cursosDisponiveis); ?> cursos →
                                    </a>
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
