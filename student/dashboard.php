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
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px;">
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
                            <div class="progress-fill" style="width: <?php echo $progressoPercentual; ?>%"></div>
                        </div>
                        <p style="text-align: center; margin-top: 10px; color: #7f8c8d;">
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
                                                <div class="progress-fill" style="width: <?php echo $progressoCurso; ?>%"></div>
                                            </div>
                                            <small style="color: #7f8c8d;">
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
                            <p class="text-muted text-center" style="padding: 40px 0;">
                                Você ainda não está inscrito em nenhum curso.<br>
                                <a href="/student/courses.php" style="color: #3498db; text-decoration: none; font-weight: 600;">
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
                                <p style="text-align: center; margin-top: 15px;">
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
    <footer style="background-color: #2c3e50; color: white; text-align: center; padding: 20px; margin-top: 50px;">
        <p>&copy; 2024 NR1 EAD. Todos os direitos reservados.</p>
    </footer>

    <style>
        .progress-bar {
            width: 100%;
            height: 30px;
            background-color: #ecf0f1;
            border-radius: 15px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #3498db, #2980b9);
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 12px;
        }

        .progress-bar-small {
            width: 100%;
            height: 8px;
            background-color: #ecf0f1;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 5px;
        }

        .progress-bar-small .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #27ae60, #229954);
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .courses-grid-small {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .course-card-small {
            border: 1px solid #ecf0f1;
            padding: 15px;
            border-radius: 4px;
            background: white;
        }

        .course-card-small h4 {
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .course-card-small p {
            color: #7f8c8d;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .course-progress {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ecf0f1;
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
