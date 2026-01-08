<?php
/**
 * 🔍 ARQUIVO DE DEBUG
 * Acesse: https://seu-dominio.com/debug.php
 */

// Habilitar exibição de TODOS os erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Definir headers
header('Content-Type: text/html; charset=utf-8');

echo "<h1>🔍 DIAGNÓSTICO DO SISTEMA</h1>";
echo "<hr>";

// 1. Versão do PHP
echo "<h2>1️⃣ Versão do PHP</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
if (phpversion() < '7.4') {
    echo "<p style='color:red;'>⚠️ AVISO: PHP muito antigo! Recomendado 8.0+</p>";
}

// 2. Extensões necessárias
echo "<h2>2️⃣ Extensões Necessárias</h2>";
$extensoes = ['mysqli', 'pdo', 'json', 'curl'];
foreach ($extensoes as $ext) {
    $status = extension_loaded($ext) ? "✅ SIM" : "❌ NÃO";
    echo "<p><strong>$ext:</strong> $status</p>";
}

// 3. Conexão ao Banco
echo "<h2>3️⃣ Conexão ao Banco de Dados</h2>";
try {
    $conn = new mysqli('localhost', 'u349967673_nr1_user', 'NovaSenha!2025', 'u349967673_nr1_ead', 3306);
    
    if ($conn->connect_error) {
        echo "<p style='color:red;'>❌ ERRO: " . htmlspecialchars($conn->connect_error) . "</p>";
    } else {
        echo "<p style='color:green;'>✅ Conexão bem-sucedida!</p>";
        
        // 4. Verificar tabelas
        echo "<h2>4️⃣ Tabelas do Banco</h2>";
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            echo "<ul>";
            while ($row = $result->fetch_row()) {
                echo "<li>" . htmlspecialchars($row[0]) . "</li>";
            }
            echo "</ul>";
        }
        
        // 5. Verificar usuários
        echo "<h2>5️⃣ Usuários no Banco</h2>";
        $result = $conn->query("SELECT id, nome, email, role FROM usuarios LIMIT 5");
        if ($result) {
            echo "<table border='1' cellpadding='10'>";
            echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Role</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color:red;'>❌ Erro ao consultar usuários: " . htmlspecialchars($conn->error) . "</p>";
        }
        
        // 6. Verificar campos especiais
        echo "<h2>6️⃣ Campos Especiais</h2>";
        $result = $conn->query("DESCRIBE aulas");
        if ($result) {
            echo "<p style='color:green;'>✅ Tabela 'aulas' existe</p>";
            echo "<ul>";
            while ($row = $result->fetch_assoc()) {
                $youtube = strpos($row['Field'], 'youtube') !== false ? "🎥" : "";
                echo "<li>" . htmlspecialchars($row['Field']) . " (" . htmlspecialchars($row['Type']) . ") $youtube</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color:red;'>❌ Tabela 'aulas' não existe</p>";
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ EXCEÇÃO: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// 7. Verificar arquivos necessários
echo "<h2>7️⃣ Arquivos Necessários</h2>";
$arquivos = [
    'includes/auth.php',
    'includes/db.php',
    'css/style-mobile-first.css',
    'dashboard.php',
    'login.php',
    'student/dashboard.php',
    'admin/users.php'
];

foreach ($arquivos as $arquivo) {
    $caminho = __DIR__ . '/' . $arquivo;
    if (file_exists($caminho)) {
        $tamanho = filesize($caminho);
        echo "<p style='color:green;'>✅ " . htmlspecialchars($arquivo) . " (" . $tamanho . " bytes)</p>";
    } else {
        echo "<p style='color:red;'>❌ " . htmlspecialchars($arquivo) . " NÃO ENCONTRADO</p>";
    }
}

// 8. Permissões de pasta
echo "<h2>8️⃣ Permissões de Pastas</h2>";
$pastas = ['uploads', 'uploads/materiais'];
foreach ($pastas as $pasta) {
    $caminho = __DIR__ . '/' . $pasta;
    if (is_dir($caminho)) {
        $perms = substr(sprintf('%o', fileperms($caminho)), -4);
        echo "<p>✅ /$pasta (Permissão: $perms)</p>";
    } else {
        echo "<p style='color:orange;'>⚠️ /$pasta não existe</p>";
    }
}

// 9. Erro log
echo "<h2>9️⃣ Log de Erros</h2>";
$error_log = __DIR__ . '/error.log';
if (file_exists($error_log)) {
    $conteudo = file_get_contents($error_log);
    if (!empty($conteudo)) {
        echo "<pre style='background:#f0f0f0;padding:10px;overflow-x:auto;max-height:300px;'>";
        echo htmlspecialchars(substr($conteudo, -2000)); // Últimas 2000 chars
        echo "</pre>";
    } else {
        echo "<p>Nenhum erro registrado (bom sinal!)</p>";
    }
} else {
    echo "<p>Arquivo de log não existe ainda</p>";
}

// 10. Teste de sessão
echo "<h2>🔟 Teste de Sessão</h2>";
session_start();
$_SESSION['teste'] = 'funcionando';
if (isset($_SESSION['teste'])) {
    echo "<p style='color:green;'>✅ Sessão funcionando</p>";
} else {
    echo "<p style='color:red;'>❌ Sessão não funcionando</p>";
}

echo "<hr>";
echo "<p><strong>Última atualização:</strong> " . date('d/m/Y H:i:s') . "</p>";
?>
