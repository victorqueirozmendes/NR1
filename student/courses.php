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

                <p>
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
                                            <form method="POST">
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
    <footer class="footer">
        <p>&copy; 2024 NR1 EAD. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
