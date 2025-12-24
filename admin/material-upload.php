<?php
/**
 * Admin - Upload de Materiais (PDFs)
 * /admin/material-upload.php
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

verificarAdmin();

$usuario = getUsuarioLogado();
$mensagem = '';
$erro = '';

// Criar diretório de uploads se não existir
$uploadDir = __DIR__ . '/../uploads/materiais/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Processar upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['arquivo'])) {
    $acao = $_POST['acao'] ?? '';
    
    if ($acao === 'upload') {
        $aulaId = $_POST['aula_id'] ?? '';
        $arquivo = $_FILES['arquivo'] ?? null;
        
        if (!$arquivo || $arquivo['error'] !== UPLOAD_ERR_OK) {
            $erro = 'Erro no upload do arquivo!';
        } elseif (strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION)) !== 'pdf') {
            $erro = 'Apenas arquivos PDF são permitidos!';
        } elseif ($arquivo['size'] > 50 * 1024 * 1024) { // 50MB max
            $erro = 'Arquivo muito grande (máximo 50MB)!';
        } else {
            // Gerar nome único para arquivo
            $nomeOriginal = pathinfo($arquivo['name'], PATHINFO_FILENAME);
            $nomeArquivo = uniqid('mat_') . '_' . time() . '.pdf';
            $caminhoCompleto = $uploadDir . $nomeArquivo;
            
            if (move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
                // Salvar informações no banco
                executeQuery($conn,
                    'INSERT INTO materiais (aula_id, titulo, arquivo, tipo, created_at) VALUES (?, ?, ?, ?, NOW())',
                    [$aulaId, $nomeOriginal, $nomeArquivo, 'pdf']
                );
                $mensagem = 'Material enviado com sucesso!';
            } else {
                $erro = 'Erro ao salvar o arquivo no servidor!';
            }
        }
    } elseif ($acao === 'deletar') {
        $materialId = $_POST['material_id'] ?? '';
        
        $material = getRow($conn, 'SELECT arquivo FROM materiais WHERE id = ?', [$materialId]);
        if ($material) {
            $caminhoCompleto = $uploadDir . $material['arquivo'];
            if (file_exists($caminhoCompleto)) {
                unlink($caminhoCompleto);
            }
            executeQuery($conn, 'DELETE FROM materiais WHERE id = ?', [$materialId]);
            $mensagem = 'Material deletado com sucesso!';
        } else {
            $erro = 'Material não encontrado!';
        }
    }
}

// Buscar aulas com materiais
$aulas = getRows($conn,
    'SELECT a.id, a.titulo, m.titulo as modulo_titulo, c.titulo as curso_titulo, COUNT(mat.id) as total_materiais
     FROM aulas a
     JOIN modulos m ON a.modulo_id = m.id
     JOIN cursos c ON m.curso_id = c.id
     LEFT JOIN materiais mat ON a.id = mat.aula_id
     GROUP BY a.id
     ORDER BY c.titulo, m.ordem, a.ordem',
    []
);

// Se tem aula_id na URL, buscar materiais dessa aula
$aulaFiltrada = null;
$materiaisAula = [];
if (isset($_GET['aula_id'])) {
    $aulaId = $_GET['aula_id'];
    $aulaFiltrada = getRow($conn, 'SELECT * FROM aulas WHERE id = ?', [$aulaId]);
    if ($aulaFiltrada) {
        $materiaisAula = getRows($conn,
            'SELECT * FROM materiais WHERE aula_id = ? ORDER BY created_at DESC',
            [$aulaId]
        );
    }
}

// Buscar todos os materiais
$todosOsMateriais = getRows($conn,
    'SELECT m.*, a.titulo as aula_titulo, mod.titulo as modulo_titulo, c.titulo as curso_titulo
     FROM materiais m
     JOIN aulas a ON m.aula_id = a.id
     JOIN modulos mod ON a.modulo_id = mod.id
     JOIN cursos c ON mod.curso_id = c.id
     ORDER BY m.created_at DESC',
    []
);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="Fazer upload de materiais para aulas - NR1 EAD">
    <meta name="theme-color" content="#3498db">
    <title>Upload de Materiais - NR1 EAD</title>
    <link rel="stylesheet" href="/css/style-mobile-first.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a href="/" class="navbar-brand">🎓 NR1 EAD Admin</a>
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
                <h3>📋 PAINEL ADMIN</h3>
                <ul class="sidebar-menu">
                    <li><a href="/dashboard.php">📊 Dashboard</a></li>
                    <li><a href="/">🏠 Voltar ao Site</a></li>
                    
                    <li style="margin-top: 20px; border-top: 1px solid #ecf0f1; padding-top: 15px;">
                        <h4 style="color: #2c3e50; margin-bottom: 10px; font-size: 12px;">GERENCIAMENTO</h4>
                    </li>
                    <li><a href="/admin/users.php">👥 Usuários</a></li>
                    <li><a href="/admin/courses.php">📚 Cursos</a></li>
                    <li><a href="/admin/modules.php">📋 Módulos</a></li>
                    <li><a href="/admin/lessons.php">📝 Aulas</a></li>
                    <li><a href="/admin/material-upload.php" class="active">📎 Materiais</a></li>
                </ul>
            </sidebar>

            <!-- Conteúdo Principal -->
            <main class="main-content">
                <h1>📎 Upload de Materiais</h1>
                
                <!-- Alertas -->
                <?php if ($mensagem): ?>
                    <div class="alert alert-success">✓ <?php echo htmlspecialchars($mensagem); ?></div>
                <?php endif; ?>
                
                <?php if ($erro): ?>
                    <div class="alert alert-danger">✗ <?php echo htmlspecialchars($erro); ?></div>
                <?php endif; ?>

                <!-- Formulário Upload -->
                <div class="card mb-30">
                    <div class="card-header">📤 Enviar Novo Material</div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" class="form">
                            <input type="hidden" name="acao" value="upload">

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div class="form-group">
                                    <label for="aula_id">Aula *</label>
                                    <select name="aula_id" id="aula_id" required>
                                        <option value="">-- Selecione uma aula --</option>
                                        <?php foreach ($aulas as $a): ?>
                                            <option value="<?php echo $a['id']; ?>" <?php echo isset($_GET['aula_id']) && $_GET['aula_id'] == $a['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($a['curso_titulo'] . ' > ' . $a['modulo_titulo'] . ' > ' . $a['titulo']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="arquivo">Arquivo PDF *</label>
                                    <input 
                                        type="file" 
                                        id="arquivo" 
                                        name="arquivo" 
                                        accept=".pdf"
                                        required
                                    >
                                    <small style="color: #7f8c8d;">Máximo 50MB</small>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                📤 Enviar Material
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Lista de Materiais -->
                <div class="card">
                    <div class="card-header">
                        📎 Materiais Enviados (<?php echo count($todosOsMateriais); ?>)
                    </div>
                    <div class="card-body">
                        <?php if (count($todosOsMateriais) > 0): ?>
                            <div style="overflow-x: auto;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Curso</th>
                                            <th>Módulo</th>
                                            <th>Aula</th>
                                            <th>Material</th>
                                            <th>Enviado em</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($todosOsMateriais as $mat): ?>
                                            <tr>
                                                <td style="font-size: 12px;">
                                                    <?php echo htmlspecialchars($mat['curso_titulo']); ?>
                                                </td>
                                                <td style="font-size: 12px;">
                                                    <?php echo htmlspecialchars($mat['modulo_titulo']); ?>
                                                </td>
                                                <td style="font-size: 12px;">
                                                    <strong><?php echo htmlspecialchars($mat['aula_titulo']); ?></strong>
                                                </td>
                                                <td>
                                                    <a href="/uploads/materiais/<?php echo htmlspecialchars($mat['arquivo']); ?>" target="_blank" class="text-primary">
                                                        📄 <?php echo htmlspecialchars($mat['titulo']); ?>
                                                    </a>
                                                </td>
                                                <td style="font-size: 12px;">
                                                    <?php echo (new DateTime($mat['created_at']))->format('d/m/Y H:i'); ?>
                                                </td>
                                                <td>
                                                    <div class="table-actions">
                                                        <a href="/uploads/materiais/<?php echo htmlspecialchars($mat['arquivo']); ?>" target="_blank" class="btn btn-secondary btn-small">
                                                            👁️ Ver
                                                        </a>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="acao" value="deletar">
                                                            <input type="hidden" name="material_id" value="<?php echo $mat['id']; ?>">
                                                            <button type="submit" class="btn btn-danger btn-small" onclick="return confirm('Deletar material?')">
                                                                🗑️ Deletar
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center" style="padding: 40px 0;">
                                Nenhum material enviado ainda.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Estatísticas -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-top: 30px;">
                    <div class="stat-box">
                        <div class="stat-number"><?php echo count($todosOsMateriais); ?></div>
                        <div class="stat-label">Materiais</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number"><?php echo count($aulas); ?></div>
                        <div class="stat-label">Aulas</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number">50 MB</div>
                        <div class="stat-label">Limite/Arquivo</div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background-color: #2c3e50; color: white; text-align: center; padding: 20px; margin-top: 50px;">
        <p>&copy; 2024 NR1 EAD. Todos os direitos reservados.</p>
    </footer>

    <style>
        select {
            padding: 8px 12px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-family: inherit;
            font-size: 14px;
            width: 100%;
        }

        input[type="file"] {
            padding: 8px 12px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            width: 100%;
        }

        .stat-box {
            background: white;
            border: 1px solid #ecf0f1;
            padding: 20px;
            border-radius: 4px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #3498db;
        }

        .stat-label {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 8px;
        }
    </style>
</body>
</html>
