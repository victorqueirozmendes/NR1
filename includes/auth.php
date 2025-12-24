<?php
/**
 * Arquivo de Autenticação
 * /includes/auth.php
 */

session_start();

// Incluir arquivo de conexão ao banco
require_once __DIR__ . '/db.php';

/**
 * Fazer Login do Usuário
 */
function login($email, $senha) {
    global $conn;
    
    // Sanitizar email
    $email = trim($email);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    
    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'sucesso' => false,
            'mensagem' => 'Email inválido.'
        ];
    }
    
    // Verificar se usuário existe
    $usuario = getRow($conn, 'SELECT id, nome, email, senha, role, ativo FROM usuarios WHERE email = ? AND ativo = 1', [$email]);
    
    if (!$usuario) {
        return [
            'sucesso' => false,
            'mensagem' => 'Email ou senha incorretos.'
        ];
    }
    
    // Verificar senha
    if (!password_verify($senha, $usuario['senha'])) {
        return [
            'sucesso' => false,
            'mensagem' => 'Email ou senha incorretos.'
        ];
    }
    
    // Criar sessão
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_role'] = $usuario['role'];
    $_SESSION['usuario_logado'] = true;
    
    return [
        'sucesso' => true,
        'mensagem' => 'Login realizado com sucesso!'
    ];
}

/**
 * Registrar Novo Usuário
 */
function registrar($nome, $email, $senha, $confirmacaoSenha) {
    global $conn;
    
    // Sanitizar dados
    $nome = trim($nome);
    $email = trim($email);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    
    // Validar nome
    if (empty($nome) || strlen($nome) < 3) {
        return [
            'sucesso' => false,
            'mensagem' => 'Nome deve ter pelo menos 3 caracteres.'
        ];
    }
    
    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'sucesso' => false,
            'mensagem' => 'Email inválido.'
        ];
    }
    
    // Validar senha
    if (empty($senha) || strlen($senha) < 6) {
        return [
            'sucesso' => false,
            'mensagem' => 'Senha deve ter pelo menos 6 caracteres.'
        ];
    }
    
    // Verificar se senhas batem
    if ($senha !== $confirmacaoSenha) {
        return [
            'sucesso' => false,
            'mensagem' => 'Senhas não conferem.'
        ];
    }
    
    // Verificar se email já existe
    $usuarioExistente = getRow($conn, 'SELECT id FROM usuarios WHERE email = ?', [$email]);
    
    if ($usuarioExistente) {
        return [
            'sucesso' => false,
            'mensagem' => 'Este email já está cadastrado.'
        ];
    }
    
    // Hash da senha
    $senhaHash = password_hash($senha, PASSWORD_BCRYPT);
    
    // Inserir usuário no banco
    $stmt = executeQuery($conn, 
        'INSERT INTO usuarios (nome, email, senha, role, ativo) VALUES (?, ?, ?, ?, ?)',
        [$nome, $email, $senhaHash, 'aluno', 0]
    );
    
    if (!$stmt) {
        return [
            'sucesso' => false,
            'mensagem' => 'Erro ao registrar usuário. Tente novamente.'
        ];
    }
    
    return [
        'sucesso' => true,
        'mensagem' => 'Usuário registrado com sucesso! Aguarde aprovação do administrador.'
    ];
}

/**
 * Fazer Logout
 */
function logout() {
    // Destruir sessão
    session_destroy();
    return true;
}

/**
 * Verificar se usuário está logado
 */
function estaLogado() {
    return isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true;
}

/**
 * Verificar se usuário é admin
 */
function ehAdmin() {
    return estaLogado() && $_SESSION['usuario_role'] === 'admin';
}

/**
 * Obter dados do usuário logado
 */
function getUsuarioLogado() {
    if (!estaLogado()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['usuario_id'],
        'email' => $_SESSION['usuario_email'],
        'nome' => $_SESSION['usuario_nome'],
        'role' => $_SESSION['usuario_role']
    ];
}

/**
 * Redirecionar se não estiver logado
 */
function verificarLogin() {
    if (!estaLogado()) {
        header('Location: /login.php');
        exit;
    }
}

/**
 * Redirecionar se não for admin
 */
function verificarAdmin() {
    if (!ehAdmin()) {
        header('Location: /index.php');
        exit;
    }
}

/**
 * Aprovar usuário (apenas admin)
 */
function aprovarUsuario($usuarioId) {
    global $conn;
    
    // Verificar se é admin
    if (!ehAdmin()) {
        return [
            'sucesso' => false,
            'mensagem' => 'Você não tem permissão para isso.'
        ];
    }
    
    // Atualizar status do usuário
    $stmt = executeQuery($conn, 
        'UPDATE usuarios SET ativo = 1 WHERE id = ? AND role = ? AND ativo = 0',
        [$usuarioId, 'aluno']
    );
    
    if (!$stmt) {
        return [
            'sucesso' => false,
            'mensagem' => 'Erro ao aprovar usuário.'
        ];
    }
    
    return [
        'sucesso' => true,
        'mensagem' => 'Usuário aprovado com sucesso!'
    ];
}

/**
 * Rejeitar usuário (apenas admin)
 */
function rejeitarUsuario($usuarioId) {
    global $conn;
    
    // Verificar se é admin
    if (!ehAdmin()) {
        return [
            'sucesso' => false,
            'mensagem' => 'Você não tem permissão para isso.'
        ];
    }
    
    // Deletar usuário
    $stmt = executeQuery($conn, 
        'DELETE FROM usuarios WHERE id = ? AND role = ? AND ativo = 0',
        [$usuarioId, 'aluno']
    );
    
    if (!$stmt) {
        return [
            'sucesso' => false,
            'mensagem' => 'Erro ao rejeitar usuário.'
        ];
    }
    
    return [
        'sucesso' => true,
        'mensagem' => 'Usuário rejeitado com sucesso!'
    ];
}

/**
 * Obter usuários pendentes de aprovação
 */
function getUsuariosPendentes() {
    global $conn;
    
    // Verificar se é admin
    if (!ehAdmin()) {
        return false;
    }
    
    return getRows($conn, 
        'SELECT id, nome, email, created_at FROM usuarios WHERE role = ? AND ativo = 0 ORDER BY created_at ASC',
        ['aluno']
    );
}

/**
 * Obter todos os usuários aprovados
 */
function getUsuariosAprovados() {
    global $conn;
    
    // Verificar se é admin
    if (!ehAdmin()) {
        return false;
    }
    
    return getRows($conn, 
        'SELECT id, nome, email, role, created_at FROM usuarios WHERE ativo = 1 ORDER BY nome ASC',
        []
    );
}

?>
