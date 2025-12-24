<?php
/**
 * Página de Login
 * /login.php
 */

require_once __DIR__ . '/includes/auth.php';

// Se já está logado, redirecionar
if (estaLogado()) {
    header('Location: /dashboard.php');
    exit;
}

$erro = '';
$sucesso = '';

// Verificar se formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    $resultado = login($email, $senha);
    
    if ($resultado['sucesso']) {
        $sucesso = $resultado['mensagem'];
        header('Location: /dashboard.php');
        exit;
    } else {
        $erro = $resultado['mensagem'];
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="description" content="Login - NR1 EAD">
    <meta name="theme-color" content="#3498db">
    <title>Login - NR1 EAD</title>
    <link rel="stylesheet" href="/css/style-mobile-first.css">
</head>
<body>
    <div class="container-auth">
        <div class="auth-box">
            <h1>Login</h1>
            <p class="subtitle">Plataforma NR1 EAD</p>
            
            <?php if ($erro): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($sucesso): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($sucesso); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="/login.php" class="form">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="seu@email.com" 
                        required
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input 
                        type="password" 
                        id="senha" 
                        name="senha" 
                        placeholder="Sua senha" 
                        required
                    >
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    Entrar
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Não tem conta? <a href="/register.php">Registre-se aqui</a></p>
                <p><a href="/">Voltar para inicio</a></p>
            </div>
        </div>
    </div>
</body>
</html>
