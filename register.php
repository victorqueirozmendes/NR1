<?php
/**
 * Página de Registro
 * /register.php
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
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirmacaoSenha = $_POST['confirmacao_senha'] ?? '';
    
    $resultado = registrar($nome, $email, $senha, $confirmacaoSenha);
    
    if ($resultado['sucesso']) {
        $sucesso = $resultado['mensagem'];
        // Limpar formulário
        $nome = '';
        $email = '';
        $senha = '';
        $confirmacaoSenha = '';
    } else {
        $erro = $resultado['mensagem'];
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="Criar nova conta na plataforma NR1 EAD">
    <meta name="theme-color" content="#3498db">
    <title>Registrar - NR1 EAD</title>
    <link rel="stylesheet" href="/css/style-mobile-first.css">
</head>
<body>
    <div class="container-auth">
        <div class="auth-box">
            <h1>Registrar</h1>
            <p class="subtitle">Crie sua conta na NR1 EAD</p>
            
            <?php if ($erro): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($sucesso): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($sucesso); ?>
                    <p style="margin-top: 10px;">
                        <a href="/login.php" style="color: inherit; text-decoration: underline;">
                            Faça login aqui
                        </a>
                    </p>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="/register.php" class="form">
                <div class="form-group">
                    <label for="nome">Nome Completo:</label>
                    <input 
                        type="text" 
                        id="nome" 
                        name="nome" 
                        placeholder="Seu nome completo" 
                        required
                        value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>"
                    >
                </div>
                
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
                        placeholder="Mínimo 6 caracteres" 
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="confirmacao_senha">Confirmar Senha:</label>
                    <input 
                        type="password" 
                        id="confirmacao_senha" 
                        name="confirmacao_senha" 
                        placeholder="Repita a senha" 
                        required
                    >
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    Registrar
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Já tem conta? <a href="/login.php">Faça login aqui</a></p>
                <p><a href="/">Voltar para inicio</a></p>
            </div>
        </div>
    </div>
</body>
</html>
