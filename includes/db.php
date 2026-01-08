<?php
/**
 * Arquivo de Conexão ao Banco de Dados
 * /includes/db.php
 * Compatível com Hostinger (PHP 8+)
 */

/* =========================
   CONFIGURAÇÕES DO BANCO
========================= */
define('DB_HOST', 'localhost');
define('DB_USER', 'u349967673_nr1_user');
define('DB_PASSWORD', 'NovaSenha!2025'); // <- a senha correta do painel
define('DB_NAME', 'u349967673_nr1_ead');
define('DB_PORT', 3306);

/* =========================
   MODO DE ERRO MYSQLI
========================= */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* =========================
   CONEXÃO
========================= */
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log("Erro MySQL: " . $e->getMessage());
    die("❌ ERRO AO CONECTAR NO BANCO: " . $e->getMessage());
}

/* =========================
   FUNÇÃO PARA EXECUTAR QUERY
========================= */
function executeQuery($conn, $sql, $params = []) {
    try {
        $stmt = $conn->prepare($sql);

        if (!empty($params)) {
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
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt;

    } catch (mysqli_sql_exception $e) {
        error_log("Erro SQL: " . $e->getMessage());
        return false;
    }
}

/* =========================
   SELECT (RESULTADO)
========================= */
function getQueryResult($conn, $sql, $params = []) {
    $stmt = executeQuery($conn, $sql, $params);
    if (!$stmt) return false;
    return $stmt->get_result();
}

/* =========================
   UMA LINHA
========================= */
function getRow($conn, $sql, $params = []) {
    $result = getQueryResult($conn, $sql, $params);
    if (!$result) return false;
    return $result->fetch_assoc();
}

/* =========================
   VÁRIAS LINHAS
========================= */
function getRows($conn, $sql, $params = []) {
    $result = getQueryResult($conn, $sql, $params);
    if (!$result) return []; // Sempre retornar array vazio, nunca false

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
}

/* =========================
   FECHAR CONEXÃO
========================= */
register_shutdown_function(function () {
    global $conn;
    if ($conn instanceof mysqli) {
        $conn->close();
    }
});
