<?php
/**
 * Arquivo de Conexão ao Banco de Dados
 * /includes/db.php
 */

// Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_USER', 'nr1_user');
define('DB_PASSWORD', 'sua_senha_aqui'); // Alterar com a senha que você criou
define('DB_NAME', 'nr1_ead');
define('DB_PORT', 3306);

// Criar conexão
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

// Verificar conexão
if ($conn->connect_error) {
    // Log de erro (não mostrar para usuário)
    error_log('Erro de conexão: ' . $conn->connect_error);
    die('Erro: Não conseguimos conectar ao banco de dados. Tente novamente mais tarde.');
}

// Definir charset para UTF-8
$conn->set_charset("utf8mb4");

// Função auxiliar para executar queries seguras
function executeQuery($conn, $sql, $params = []) {
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log('Erro ao preparar query: ' . $conn->error);
        return false;
    }
    
    if (!empty($params)) {
        // Determinar tipos de dados
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        
        // Bind parameters
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        error_log('Erro ao executar query: ' . $stmt->error);
        return false;
    }
    
    return $stmt;
}

// Função para obter resultado de uma query SELECT
function getQueryResult($conn, $sql, $params = []) {
    $stmt = executeQuery($conn, $sql, $params);
    
    if (!$stmt) {
        return false;
    }
    
    $result = $stmt->get_result();
    return $result;
}

// Função para obter uma única linha
function getRow($conn, $sql, $params = []) {
    $result = getQueryResult($conn, $sql, $params);
    
    if (!$result) {
        return false;
    }
    
    return $result->fetch_assoc();
}

// Função para obter múltiplas linhas
function getRows($conn, $sql, $params = []) {
    $result = getQueryResult($conn, $sql, $params);
    
    if (!$result) {
        return false;
    }
    
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    
    return $rows;
}

// Fechar conexão ao final do script
register_shutdown_function(function() {
    global $conn;
    if ($conn) {
        $conn->close();
    }
});

?>
