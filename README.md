# 🎓 NR1 EAD — Plataforma de Educação a Distância

Plataforma completa de educação a distância construída com **PHP + HTML + CSS** (sem frameworks).

**Stack Tech**: PHP 7.4+ | MySQL 5.7+ | HTML5 | CSS3 (Mobile-First) | MySQLi Prepared Statements

---

## 📊 Status do Projeto

### ✅ ETAPA 1 — Autenticação (Completo)
- [x] Sistema de login com bcrypt
- [x] Registro de usuários
- [x] Aprovação de usuários (admin)
- [x] Logout seguro
- [x] Verificação de permissões (role-based)

**Documentação**: Integrado no código via comentários

---

### ✅ ETAPA 2 — Painel Admin (Completo)

**Páginas**: 5 módulos de gerenciamento

- [x] **Users** (`/admin/users.php`) — Aprovar/promover usuários, estatísticas
- [x] **Courses** (`/admin/courses.php`) — CRUD de cursos, grid de cards
- [x] **Modules** (`/admin/modules.php`) — Criar/editar/deletar módulos por curso
- [x] **Lessons** (`/admin/lessons.php`) — Criar aulas com editor HTML
- [x] **Materials** (`/admin/material-upload.php`) — Upload de PDFs para aulas

**Documentação**: `ETAPA2_ADMIN.md`

---

### ✅ ETAPA 3 — Backend Estudante (Completo)

**Páginas**: 4 módulos estudante + 2 novas tabelas

- [x] **Dashboard** (`/student/dashboard.php`) — Visão geral do aluno, progresso
- [x] **Courses** (`/student/courses.php`) — Explorar e se inscrever em cursos
- [x] **Course Detail** (`/student/course.php`) — Estrutura de módulos/aulas
- [x] **Lesson** (`/student/lesson.php`) — Visualizar aula, materiais, comentários
- [x] **Tabela `progresso`** — Rastrear conclusão de aulas (usuario_id, aula_id, completado, data_conclusao)
- [x] **Tabela `comentarios`** — Comentários por aula (usuario_id, aula_id, comentario, oculto)

**Documentação**: `ETAPA3_ALUNO.md`

---

### ✅ ETAPA 4 — Interface Mobile-First (Completo)

**Objetivo**: Otimizar interface para **80% dos alunos que usam celular**.

- [x] **Framework CSS Mobile-First** (`/css/style-mobile-first.css`)
  - Responsive containers (100% → 1200px max)
  - Flexbox para layouts lineares
  - CSS Grid para layouts 2D
  - Media queries em 768px (tablet) e 1024px (desktop)
  - Touch-optimized buttons (44px mínimo)
  - Responsive typography
  - CSS Variables para theming
  
- [x] **Páginas Atualizadas** (11 arquivos)
  - ✅ `/login.php` — Viewport meta tags + novo CSS
  - ✅ `/register.php` — Novo CSS
  - ✅ `/index.php` — Novo CSS
  - ✅ `/dashboard.php` — Novo CSS
  - ✅ `/student/dashboard.php` — Novo CSS
  - ✅ `/student/courses.php` — Novo CSS
  - ✅ `/student/course.php` — Novo CSS
  - ✅ `/student/lesson.php` — Novo CSS + HTML otimizado
  - ✅ `/admin/users.php` — Novo CSS
  - ✅ `/admin/courses.php` — Novo CSS
  - ✅ `/admin/modules.php` — Novo CSS
  - ✅ `/admin/lessons.php` — Novo CSS
  - ✅ `/admin/material-upload.php` — Novo CSS
  - ✅ `/admin/usuarios.php` — Novo CSS

- [x] **Otimizações de Componentes**
  - Breadcrumb navigation responsivo
  - Materials list mobile-friendly
  - Comments section responsivo
  - Lesson navigation (2 colunas)
  - Cards grid (1 → 2 → 3 colunas)
  - Forms full-width em mobile

**Documentação**: `ETAPA4_MOBILE.md` (Completo com exemplos e wireframes)

---

## 📂 Estrutura do Projeto

```
NR1/
├── README.md                          # Este arquivo
├── ETAPA2_ADMIN.md                    # Documentação Painel Admin
├── ETAPA3_ALUNO.md                    # Documentação Backend Estudante
├── ETAPA4_MOBILE.md                   # Documentação Mobile-First
│
├── css/
│   ├── style.css                      # CSS antigo (deprecado)
│   └── style-mobile-first.css         # CSS mobile-first (NOVO) 📱
│
├── includes/
│   ├── db.php                         # Conexão MySQLi + helpers
│   └── auth.php                       # 11 funções de autenticação
│
├── uploads/
│   └── materiais/                     # PDFs enviados
│
├── admin/
│   ├── users.php                      # Gerenciar usuários
│   ├── courses.php                    # CRUD cursos
│   ├── modules.php                    # CRUD módulos
│   ├── lessons.php                    # CRUD aulas
│   ├── material-upload.php            # Upload de materiais
│   └── usuarios.php                   # Legacy (deprecated)
│
├── student/
│   ├── dashboard.php                  # Dashboard do aluno
│   ├── courses.php                    # Explorar cursos
│   ├── course.php                     # Detalhes do curso
│   └── lesson.php                     # Visualizar aula
│
└── *.php                              # Raiz
    ├── index.php                      # Home / redirect
    ├── login.php                      # Login
    ├── register.php                   # Registrar
    ├── logout.php                     # Logout
    └── dashboard.php                  # Dashboard padrão
```

---

## 🗄️ Banco de Dados

### Tabelas (8 total)

```sql
usuarios
├── id (pk)
├── nome
├── email (unique)
├── senha (bcrypted)
├── role (admin|aluno) default='aluno'
├── ativo (0|1) default=0
└── created_at

cursos
├── id (pk)
├── titulo
├── descricao
├── instrutor
└── created_at

modulos
├── id (pk)
├── curso_id (fk)
├── titulo
├── descricao
├── ordem
└── created_at

aulas
├── id (pk)
├── modulo_id (fk)
├── titulo
├── conteudo (html)
├── ordem
└── created_at

materiais
├── id (pk)
├── aula_id (fk)
├── titulo
├── arquivo (filename)
├── tipo (pdf|doc|etc)
└── created_at

acessos
├── id (pk)
├── usuario_id (fk)
├── curso_id (fk)
└── created_at

progresso
├── id (pk)
├── usuario_id (fk)
├── aula_id (fk)
├── completado (0|1) default=0
└── data_conclusao

comentarios
├── id (pk)
├── usuario_id (fk)
├── aula_id (fk)
├── comentario
├── oculto (0|1) default=0
└── created_at
```

### Script de Criação

```sql
-- Executar em MySQL:
-- mysql -u user -p database < create-tables.sql
-- Consultar ETAPA3_ALUNO.md para create-tables-etapa3.sql
```

---

## 🔐 Segurança

- ✅ **Passwords**: PHP `password_hash()` (bcrypt)
- ✅ **SQL Injection**: MySQLi prepared statements (`?` placeholders)
- ✅ **Session**: PHP `$_SESSION` com validação
- ✅ **Role-Based Access**: Verificação em cada página
- ✅ **HTTPS Ready**: Meta tags + redirects
- ✅ **CORS**: Não aplicável (mesma origem)

---

## 🧪 Teste Rápido

### 1. Setup

```bash
# Criar banco de dados
mysql -u root -p
> CREATE DATABASE nr1_ead;
> EXIT;

# Importar tabelas
mysql -u root -p nr1_ead < create-tables.sql
mysql -u root -p nr1_ead < create-tables-etapa3.sql

# Editar credenciais
nano includes/db.php
# Ajustar: $dbhost, $dbuser, $dbpass, $dbname
```

### 2. Usuários Padrão

- **Admin**: `admin@nr1.com` / `123456` (pré-criado)
- **Aluno**: Registre-se em `/register.php` (precisa aprovação)

### 3. Fluxo

1. Abrir `http://localhost/NR1/` → Home
2. Login com admin ou aluno
3. Admin: `/admin/users.php` → Aprovar aluno
4. Aluno: `/student/courses.php` → Inscrever-se
5. Aluno: `/student/lesson.php?id=1` → Ver aula

---

## 📱 Mobile-First Design

### Breakpoints

| Dispositivo | Largura | CSS |
|---|---|---|
| **Mobile** | < 768px | Defaults (sem media query) |
| **Tablet** | 768px - 1023px | `@media (min-width: 768px)` |
| **Desktop** | ≥ 1024px | `@media (min-width: 1024px)` |

### Componentes Responsivos

- **Containers**: 100% mobile → max-width 700px tablet → 1200px desktop
- **Cards**: 1 coluna mobile → 2 colunas tablet → 3 colunas desktop
- **Buttons**: min-height 44px (touch-friendly)
- **Forms**: full-width mobile → max-width 600px desktop
- **Navbar**: compact mobile (12px padding) → expanded desktop (24px padding)
- **Sidebar**: hidden mobile → sticky tablet+ (200px-250px)

### Viewport Meta Tags

```html
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
<meta name="theme-color" content="#3498db">
```

---

## 🚀 Próximas Melhorias

### ETAPA 4.1 — Interatividade
- [ ] Hamburger menu (mobile)
- [ ] Sticky navbar (desktop)
- [ ] Comment pagination
- [ ] Form validation (JavaScript)
- [ ] Lazy load images

### ETAPA 4.2 — Performance
- [ ] Minify CSS/JS
- [ ] WebP images
- [ ] Service Worker (offline)
- [ ] PWA manifest
- [ ] Page caching

### ETAPA 4.3 — Recursos Avançados
- [ ] Video player responsivo
- [ ] Notificações via email
- [ ] Certificados (PDF)
- [ ] Quiz/Avaliações
- [ ] Gamificação (badges/pontos)

### ETAPA 4.4 — Admin Dashboard
- [ ] Charts (estatísticas)
- [ ] Dark mode
- [ ] Bulk actions
- [ ] Export (CSV/PDF)
- [ ] Analytics

---

## 📞 Credenciais de Teste

| Campo | Valor |
|---|---|
| **URL Base** | `http://localhost/NR1/` |
| **Banco** | `nr1_ead` |
| **Admin Email** | `admin@nr1.com` |
| **Admin Senha** | `123456` |
| **BD Host** | `localhost` |
| **BD User** | `root` |
| **BD Pass** | (sua senha) |

---

## 📖 Documentação

- **ETAPA2_ADMIN.md** — 5 páginas de admin, CRUD completo
- **ETAPA3_ALUNO.md** — Backend estudante, progresso, comentários
- **ETAPA4_MOBILE.md** — Responsive design, media queries, exemplos

Todos os arquivos PHP incluem comentários detalhados.

---

## ✨ Highlights

✅ **100% Funcional** — Todas as 4 etapas completas
✅ **Mobile-First** — Otimizado para 80% dos alunos em celular
✅ **Sem Framework** — PHP/HTML/CSS puro (fácil manutenção)
✅ **Seguro** — Prepared statements, bcrypt, session-based auth
✅ **Responsivo** — Funciona em 320px até 2560px
✅ **Acessível** — Touch targets 44px+, zoom permitido
✅ **Bem Documentado** — Comentários no código + 3 guias

---

## 📝 Licença

Projeto educacional — Use livremente

---

**Última Atualização**: Janeiro/2024
**Versão**: 1.0
**Status**: ✅ MVP Completo (Mobile-First)
