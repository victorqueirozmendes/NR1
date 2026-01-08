<?php
/**
 * Teste de Erros
 */

// Habilitar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

echo "<h1>🔍 TESTE DE ERROS</h1>";

// Teste 1: Conexão
echo "<h2>1. Conexão ao Banco</h2>";
try {
    if ($conn && $conn->ping()) {
        echo "✅ Conexão OK<br>";
    } else {
        echo "❌ Conexão FALHOU<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}

// Teste 2: Tabelas
echo "<h2>2. Verificar Tabelas</h2>";
$tabelas = ['usuarios', 'cursos', 'modulos', 'aulas', 'materiais', 'acessos', 'progresso'];
foreach ($tabelas as $tabela) {
    $result = $conn->query("SHOW TABLES LIKE '$tabela'");
    if ($result && $result->num_rows > 0) {
        echo "✅ Tabela '$tabela' existe<br>";
    } else {
        echo "❌ Tabela '$tabela' NÃO existe<br>";
    }
}

// Teste 3: Campos em aulas
echo "<h2>3. Verificar Campos da Tabela 'aulas'</h2>";
$result = $conn->query("DESCRIBE aulas");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
    }
} else {
    echo "❌ Erro ao descrever tabela<br>";
}

// Teste 4: Contar registros
echo "<h2>4. Contar Registros</h2>";
$usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc();
$cursos = $conn->query("SELECT COUNT(*) as total FROM cursos")->fetch_assoc();
$aulas = $conn->query("SELECT COUNT(*) as total FROM aulas")->fetch_assoc();

echo "Usuários: " . $usuarios['total'] . "<br>";
echo "Cursos: " . $cursos['total'] . "<br>";
echo "Aulas: " . $aulas['total'] . "<br>";

// Teste 5: Funções de autenticação
echo "<h2>5. Funções de Autenticação</h2>";
echo "estaLogado(): " . (estaLogado() ? "SIM" : "NÃO") . "<br>";
echo "ehAdmin(): " . (ehAdmin() ? "SIM" : "NÃO") . "<br>";
?>
