<?php
/**
 * 🧪 TESTE SIMPLES
 * Acesse: https://seu-dominio.com/test.php
 */

// Mostrar TODOS os erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 TESTE RÁPIDO</h1>";

// Teste 1: PHP funcionando
echo "<p>✅ PHP está funcionando</p>";

// Teste 2: Testar conexão direto
echo "<h2>Testando conexão ao banco...</h2>";

$host = 'localhost';
$user = 'u349967673_nr1_user';
$pass = 'NovaSenha!2025';
$db = 'u349967673_nr1_ead';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("<p style='color:red;'><strong>❌ ERRO AO CONECTAR:</strong> " . mysqli_connect_error() . "</p>");
}

echo "<p style='color:green;'><strong>✅ Conexão ao banco bem-sucedida!</strong></p>";

// Teste 3: Contar usuários
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM usuarios");
$row = mysqli_fetch_assoc($result);
echo "<p>Total de usuários: <strong>" . $row['total'] . "</strong></p>";

// Teste 4: Listar 3 usuários
echo "<h2>Últimos usuários:</h2>";
$result = mysqli_query($conn, "SELECT id, nome, email FROM usuarios LIMIT 3");
echo "<table border='1'><tr><th>ID</th><th>Nome</th><th>Email</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr><td>" . $row['id'] . "</td><td>" . htmlspecialchars($row['nome']) . "</td><td>" . htmlspecialchars($row['email']) . "</td></tr>";
}
echo "</table>";

// Teste 5: Verificar arquivo auth.php
echo "<h2>Verificando arquivos...</h2>";
if (file_exists(__DIR__ . '/includes/auth.php')) {
    echo "<p>✅ /includes/auth.php existe</p>";
} else {
    echo "<p>❌ /includes/auth.php NÃO EXISTE</p>";
}

if (file_exists(__DIR__ . '/includes/db.php')) {
    echo "<p>✅ /includes/db.php existe</p>";
} else {
    echo "<p>❌ /includes/db.php NÃO EXISTE</p>";
}

if (file_exists(__DIR__ . '/dashboard.php')) {
    echo "<p>✅ /dashboard.php existe</p>";
} else {
    echo "<p>❌ /dashboard.php NÃO EXISTE</p>";
}

mysqli_close($conn);

echo "<hr>";
echo "<p><a href='/login.php'>🔓 Ir para Login</a></p>";
echo "<p><a href='/'>🏠 Ir para Início</a></p>";
?>
