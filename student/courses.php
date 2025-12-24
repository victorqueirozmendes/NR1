<?php
/**
 * Explorar Cursos
 * /student/courses.php
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

verificarLogin();

$usuario = getUsuarioLogado();

// Se for admin, redirecionar
if ($usuario['role'] === 'admin') {
    header('Location: /admin/users.php');
    exit;
}

$mensagem = '';

// Processar inscrição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cursoId = $_POST['curso_id'] ?? '';
    
    // Verificar se já está inscrito
    $jaInscrito = getRow($conn,
        'SELECT id FROM acessos WHERE usuario_id = ? AND curso_id = ?',
        [$usuario['id'], $cursoId]
    );
    
    if (!$jaInscrito) {
        executeQuery($conn,
            'INSERT INTO acessos (usuario_id, curso_id, created_at) VALUES (?, ?, NOW())',
            [$usuario['id'], $cursoId]
        );
        $mensagem = 'Inscrição realizada com sucesso! Você pode começar a estudar.';
    }
}

// Buscar cursos já inscritos
$cursosInscritos = getRows($conn,
    'SELECT curso_id FROM acessos WHERE usuario_id = ?',
    [$usuario['id']]
);

$idsInscritos = array_map(fn($c) => $c['curso_id'], $cursosInscritos);

// Buscar todos os cursos
$todosOsCursos = getRows($conn,
    'SELECT * FROM cursos ORDER BY created_at DESC',
    []
);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="Explorar e se inscrever em cursos - NR1 EAD">
    <meta name="theme-color" content="#3498db">
    <title>Explorar Cursos - NR1 EAD</title>
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
                    <li><a href="/student/courses.php" class="active">📚 Explorar Cursos</a></li>
                    <li><a href="/">🏠 Voltar ao Site</a></li>
                </ul>
            </aside>

            <!-- Conteúdo Principal -->
            <main class="main-content">
                <h1>📚 Explorar Cursos</h1>
                
                <?php if ($mensagem): ?>
                    <div class="alert alert-success">✓ <?php echo htmlspecialchars($mensagem); ?></div>
                <?php endif; ?>

                <p style="color: #7f8c8d; margin-bottom: 30px;">
                    Descubra nossos cursos e comece a aprender. Clique em um curso para se inscrever.
                </p>

                <!-- Grade de Cursos -->
                <?php if (count($todosOsCursos) > 0): ?>
                    <div class="courses-grid-large">
                        <?php foreach ($todosOsCursos as $curso):
                            $estaInscrito = in_array($curso['id'], $idsInscritos);
                            
                            // Contar módulos e aulas
                            $totalModulos = getRow($conn,
                                'SELECT COUNT(*) as total FROM modulos WHERE curso_id = ?',
                                [$curso['id']]
                            );
                            
                            $totalAulas = getRow($conn,
                                'SELECT COUNT(au.id) as total FROM aulas au
                                 INNER JOIN modulos m ON au.modulo_id = m.id
                                 WHERE m.curso_id = ?',
                                [$curso['id']]
                            );
                        ?>
                            <div class="course-card-large">
                                <div class="course-thumbnail">
                                    <div class="course-icon">📚</div>
                                    <?php if ($estaInscrito): ?>
                                        <div class="course-badge">✓ Inscrito</div>
                                    <?php endif; ?>
                                </div>

                                <div class="course-body">
                                    <h3><?php echo htmlspecialchars($curso['titulo']); ?></h3>
                                    
                                    <p class="course-description">
                                        <?php echo htmlspecialchars($curso['descricao']); ?>
                                    </p>

                                    <div class="course-info">
                                        <span class="course-info-item">
                                            📋 <?php echo $totalModulos['total']; ?> módulo(s)
                                        </span>
                                        <span class="course-info-item">
                                            📝 <?php echo $totalAulas['total']; ?> aula(s)
                                        </span>
                                    </div>

                                    <div class="course-footer">
                                        <?php if ($estaInscrito): ?>
                                            <a href="/student/course.php?id=<?php echo $curso['id']; ?>" class="btn btn-primary btn-block">
                                                Continuar Estudando
                                            </a>
                                        <?php else: ?>
                                            <form method="POST" style="width: 100%;">
                                                <input type="hidden" name="curso_id" value="<?php echo $curso['id']; ?>">
                                                <button type="submit" class="btn btn-success btn-block">
                                                    Inscrever-se
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        ℹ️ Ainda não há cursos disponíveis. Volte em breve!
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
        .courses-grid-large {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }

        .course-card-large {
            background: white;
            border: 1px solid #ecf0f1;
            border-radius: 4px;
            overflow: hidden;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        .course-card-large:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            transform: translateY(-4px);
        }

        .course-thumbnail {
            background: linear-gradient(135deg, #3498db, #2980b9);
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .course-icon {
            font-size: 48px;
        }

        .course-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #27ae60;
            color: white;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .course-body {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .course-body h3 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .course-description {
            color: #7f8c8d;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 15px;
            flex: 1;
        }

        .course-info {
            display: flex;
            gap: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ecf0f1;
            margin-bottom: 15px;
        }

        .course-info-item {
            color: #7f8c8d;
            font-size: 12px;
        }

        .course-footer {
            display: flex;
            gap: 10px;
        }

        .course-footer .btn {
            flex: 1;
            margin: 0;
        }
    </style>
</body>
</html>
