# 🎓 NR1 EAD - Plataforma de Educação a Distância

Uma plataforma completa de educação a distância desenvolvida em PHP com MySQL, featuring cursos, módulos, aulas, materiais de suporte e sistema de controle de acesso.

## ✨ Recursos Principais

- ✅ **Autenticação Segura**: Login/Register com bcrypt hash
- ✅ **Gerenciamento de Cursos**: Admin pode criar cursos, módulos e aulas
- ✅ **Suporte a Vídeos YouTube**: Incorpore vídeos do YouTube direto nas aulas
- ✅ **Materiais de Download**: PDFs para download nas aulas
- ✅ **Rastreamento de Progresso**: Acompanhamento de conclusão de aulas
- ✅ **Sistema de Comentários**: Alunos podem comentar nas aulas
- ✅ **Controle de Acesso por Usuário**: Admin controla quem vê o quê
- ✅ **Design Responsivo**: Mobile-first, funciona em todos os dispositivos
- ✅ **Design Futurista**: Interface moderna com cores azul/preto/branco e gradientes

## 🚀 Instalação

### Pré-requisitos
- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx

### Passos

1. **Clonar o repositório**
```bash
git clone https://github.com/seu-usuario/nr1-ead.git
cd nr1-ead
```

2. **Criar banco de dados**
```bash
mysql -u root -p < database-init.sql
```

3. **Aplicar atualizações (YouTube + Controle de Acesso)**
```bash
mysql -u root -p NR1 < database-updates.sql
```

4. **Configurar conexão ao banco**
Edite `/includes/db.php` com suas credenciais:
```php
$servername = "localhost";
$username = "seu_usuario";
$password = "sua_senha";
$database = "NR1";
```

5. **Configurar diretório de uploads**
```bash
mkdir -p /uploads/materiais
chmod 755 /uploads/materiais
```

6. **Acessar a plataforma**
- Acesse `http://seu-dominio.com`
- Padrão: Email: `admin@nr1.com` | Senha: `123456`

## 📋 Estrutura do Projeto

```
nr1-ead/
├── index.php                 # Página inicial
├── dashboard.php             # Dashboard do usuário
├── login.php                 # Página de login
├── register.php              # Página de registro
├── logout.php                # Logout
│
├── admin/
│   ├── users.php            # Gerenciar usuários
│   ├── courses.php          # Gerenciar cursos
│   ├── modules.php          # Gerenciar módulos
│   ├── lessons.php          # Gerenciar aulas + YouTube
│   ├── material-upload.php  # Upload de materiais (PDFs)
│   └── access-control.php   # Controle de acesso por usuário
│
├── student/
│   ├── dashboard.php        # Dashboard do aluno
│   ├── courses.php          # Lista de cursos inscritos
│   ├── course.php           # Detalhes do curso
│   └── lesson.php           # Visualizar aula + vídeo YouTube
│
├── includes/
│   ├── auth.php             # Funções de autenticação
│   └── db.php               # Conexão e funções do banco
│
├── css/
│   └── style-mobile-first.css # Estilos responsivos
│
├── uploads/
│   └── materiais/           # PDFs dos materiais
│
└── database-updates.sql     # Atualizações de banco (YouTube + Acesso)
```

## 🗄️ Estrutura do Banco de Dados

### Tabelas Principais

#### `usuarios`
```sql
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    role ENUM('admin', 'aluno') DEFAULT 'aluno',
    ativo BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### `cursos`
```sql
CREATE TABLE cursos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    instrutor VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### `modulos`
```sql
CREATE TABLE modulos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    curso_id INT NOT NULL FK,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    ordem INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### `aulas`
```sql
CREATE TABLE aulas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    modulo_id INT NOT NULL FK,
    titulo VARCHAR(255) NOT NULL,
    conteudo LONGTEXT,
    youtube_url VARCHAR(255),  # ← NOVO: URL do vídeo YouTube
    ordem INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### `materiais`
```sql
CREATE TABLE materiais (
    id INT PRIMARY KEY AUTO_INCREMENT,
    aula_id INT NOT NULL FK,
    titulo VARCHAR(255),
    arquivo VARCHAR(255),
    tipo VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### `user_access_control` (NOVO)
```sql
CREATE TABLE user_access_control (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL FK,
    curso_id INT NOT NULL FK,
    access_level ENUM('completo', 'limitado', 'bloqueado') DEFAULT 'completo',
    pode_ver_materiais BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (usuario_id, curso_id)
);
```

#### `progresso`
```sql
CREATE TABLE progresso (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL FK,
    aula_id INT NOT NULL FK,
    completado BOOLEAN DEFAULT 0,
    data_conclusao TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### `comentarios`
```sql
CREATE TABLE comentarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL FK,
    aula_id INT NOT NULL FK,
    comentario TEXT,
    oculto BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 🔐 Controle de Acesso

### Níveis de Acesso
- **completo**: Acesso total ao curso
- **limitado**: Acesso restrito (sem materiais ou parcial)
- **bloqueado**: Sem acesso ao curso

### Funções de Verificação
```php
// Verificar se usuário pode acessar curso
podeAcessarCurso($usuarioId, $cursoId)

// Verificar se usuário pode acessar materiais
podeAcessarMateriais($usuarioId, $cursoId)

// Registrar usuário no curso
registrarUsuarioNoCurso($usuarioId, $cursoId, 'completo')

// Atualizar permissões
atualizarPermissaoUsuario($usuarioId, $cursoId, ['access_level' => 'limitado', 'pode_ver_materiais' => false])

// Remover acesso
removerAccessoCurso($usuarioId, $cursoId)
```

## 🎥 Como Adicionar Vídeos YouTube

### No Admin (Criar/Editar Aula)
1. Acesse **Admin → Aulas**
2. Crie ou edite uma aula
3. Preencha o campo "URL do Vídeo YouTube"
4. Exemplo: `https://www.youtube.com/watch?v=xxxxx`

### Para o Aluno
O vídeo será exibido automaticamente na aula com player responsivo.

## 👤 Admin - Controle de Acesso

### Como Configurar Quem Vê O Quê

1. Acesse **Admin → Acesso de Usuários**
2. Selecione um curso
3. Escolha qual usuário adicionar
4. Configure:
   - **Nível**: Completo/Limitado/Bloqueado
   - **Ver Materiais**: Sim/Não
5. Clique em "Atualizar" ou "Remover"

## 🎨 Design & Cores

- **Primária**: `#3498db` (Azul)
- **Secundária**: `#2c3e50` (Preto)
- **Fundo**: `#ffffff` (Branco)
- **Efeitos**: Gradientes, sombras, transições suaves

## 📱 Responsividade

- ✅ Mobile (320px+)
- ✅ Tablet (768px+)
- ✅ Desktop (1024px+)

## 🛠️ Tecnologias

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript vanilla
- **Auth**: bcrypt password hashing
- **Framework**: Procedural PHP com prepared statements

## 📄 Licença

MIT License - Sinta-se livre para usar e modificar

## 👨‍💻 Suporte

Para dúvidas ou issues, abra um [GitHub Issue](https://github.com/seu-usuario/nr1-ead/issues)

---

**Desenvolvido com ❤️ para educação digital**
