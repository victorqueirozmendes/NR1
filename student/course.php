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
                    <li style="margin-top: 20px; border-top: 1px solid #ecf0f1; padding-top: 15px;">
                        <h4 style="color: #2c3e50; margin-bottom: 10px; font-size: 12px;">MÓDULOS</h4>
                    </li>
                    <?php foreach ($modulos as $mod): ?>
                        <li>
                            <a href="#modulo-<?php echo $mod['id']; ?>" style="font-size: 12px;">
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
                    <p style="color: #7f8c8d; margin-bottom: 20px;">
                        <?php echo htmlspecialchars($curso['descricao']); ?>
                    </p>
                    
                    <div style="display: grid; grid-template-columns: auto auto auto; gap: 20px; margin-bottom: 20px;">
                        <div class="stat-box" style="min-width: 150px;">
                            <div class="stat-number"><?php echo count($modulos); ?></div>
                            <div class="stat-label">Módulos</div>
                        </div>
                        <div class="stat-box" style="min-width: 150px;">
                            <div class="stat-number"><?php echo $totalAulas['total']; ?></div>
                            <div class="stat-label">Aulas</div>
                        </div>
                        <div class="stat-box" style="min-width: 150px;">
                            <div class="stat-number"><?php echo $progressoPercentual; ?>%</div>
                            <div class="stat-label">Progresso</div>
                        </div>
                    </div>

                    <!-- Barra de Progresso -->
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $progressoPercentual; ?>%"></div>
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
                            <span style="float: right; font-weight: normal; font-size: 12px;">
                                <?php echo count($aulas); ?> aula(s)
                            </span>
                        </div>
                        
                        <?php if (!empty($modulo['descricao'])): ?>
                            <div class="card-body" style="padding-bottom: 0; border-bottom: 1px solid #ecf0f1;">
                                <p style="color: #7f8c8d; font-size: 13px;">
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
    <footer style="background-color: #2c3e50; color: white; text-align: center; padding: 20px; margin-top: 50px;">
        <p>&copy; 2024 NR1 EAD. Todos os direitos reservados.</p>
    </footer>

    <style>
        .course-header-detail {
            background: white;
            border: 1px solid #ecf0f1;
            border-radius: 4px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .course-header-detail h1 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

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

        .lessons-list-course {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .lesson-item-course {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border: 1px solid #ecf0f1;
            border-radius: 4px;
            background: white;
            transition: all 0.3s;
        }

        .lesson-item-course:hover {
            background-color: #f9f9f9;
            border-color: #3498db;
        }

        .lesson-item-course.completed {
            opacity: 0.7;
            background-color: #f0f9f7;
        }

        .lesson-checkbox {
            flex-shrink: 0;
        }

        .checkbox-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border: 2px solid #bdc3c7;
            border-radius: 50%;
            font-weight: 600;
            color: #bdc3c7;
            font-size: 14px;
        }

        .checkbox-icon.completed {
            background-color: #27ae60;
            border-color: #27ae60;
            color: white;
        }

        .lesson-info {
            flex: 1;
        }

        .lesson-info h4 {
            color: #2c3e50;
            margin: 0 0 5px 0;
            font-size: 14px;
        }

        .lesson-info p {
            color: #7f8c8d;
            font-size: 12px;
            margin: 0;
        }

        .lesson-item-course .btn {
            flex-shrink: 0;
        }

        .stat-box {
            background: white;
            border: 1px solid #ecf0f1;
            padding: 15px;
            border-radius: 4px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #3498db;
        }

        .stat-label {
            font-size: 11px;
            color: #7f8c8d;
            margin-top: 5px;
        }
    </style>
</body>
</html>
