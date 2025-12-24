<?php
/**
 * Página Inicial
 * /index.php
 */

require_once __DIR__ . '/includes/auth.php';

// Se está logado, redirecionar para dashboard
if (estaLogado()) {
    header('Location: /dashboard.php');
    exit;
}

// Verificar se há mensagem na URL
$mensagem = $_GET['mensagem'] ?? '';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="Plataforma de Educação a Distância - NR1 EAD">
    <meta name="theme-color" content="#3498db">
    <title>NR1 EAD - Plataforma de Educação a Distância</title>
    <link rel="stylesheet" href="/css/style-mobile-first.css">
    <style>
        .hero {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            text-align: center;
            padding: 100px 20px;
        }
        
        .hero h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }
        
        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin: 50px 0;
        }
        
        .feature-box {
            background: white;
            padding: 30px;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .feature-box h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .feature-box p {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .cta-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .footer-simple {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 30px 20px;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <div class="hero">
        <h1>🎓 NR1 EAD</h1>
        <p>Plataforma de Educação a Distância</p>
        <div class="cta-buttons">
            <a href="/login.php" class="btn btn-primary">Entrar</a>
            <a href="/register.php" class="btn btn-secondary">Registrar</a>
        </div>
    </div>

    <!-- Logout Success Message -->
    <?php if ($mensagem === 'logout_sucesso'): ?>
        <div class="container mt-30">
            <div class="alert alert-success text-center">
                ✓ Você saiu com sucesso! Até logo!
            </div>
        </div>
    <?php endif; ?>

    <!-- Features -->
    <div class="container">
        <div class="features">
            <div class="feature-box">
                <h3>📚 Cursos Online</h3>
                <p>Acesse diversos cursos de qualidade, desenvolvidos por especialistas em suas áreas.</p>
            </div>
            <div class="feature-box">
                <h3>🎯 Aprendizado Flexível</h3>
                <p>Estude no seu próprio ritmo, quando e onde quiser, com acessibilidade total.</p>
            </div>
            <div class="feature-box">
                <h3>📊 Acompanhamento</h3>
                <p>Monitore seu progresso em tempo real e receba feedback personalizado.</p>
            </div>
            <div class="feature-box">
                <h3>🏆 Certificados</h3>
                <p>Obtenha certificados reconhecidos ao completar seus cursos.</p>
            </div>
        </div>

        <!-- CTA Section -->
        <div>
            <h2>Comece sua Jornada de Aprendizado Agora</h2>
            <p>
                Junte-se a milhares de alunos e desenvolva novas habilidades.
            </p>
            <a href="/register.php" class="btn btn-primary btn-block">
                Criar Conta Gratuita
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer-simple">
        <p>&copy; 2024 NR1 EAD. Todos os direitos reservados.</p>
        <p>
            <a href="#">Política de Privacidade</a> | 
            <a href="#">Termos de Serviço</a>
        </p>
    </footer>
</body>
</html>
