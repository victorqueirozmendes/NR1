# 🗺️ MAPA DE NAVEGAÇÃO COMPLETO

## 📊 Fluxograma Geral

```
┌─────────────────────────────────────────────────────────────────┐
│                        HOMEPAGE (/index.php)                    │
│  - Descrição da plataforma                                      │
│  - Botões: Login / Registrar                                    │
└────────────┬──────────────────────────────────┬─────────────────┘
             │                                  │
             ▼                                  ▼
    ┌──────────────────┐          ┌──────────────────────┐
    │  LOGIN           │          │  REGISTER            │
    │  (/login.php)    │          │  (/register.php)     │
    │  - Email         │          │  - Nome              │
    │  - Senha         │          │  - Email             │
    │  - Entrar        │          │  - Senha             │
    │  - Registrar     │          │  - Registrar         │
    └────────┬─────────┘          └──────────┬───────────┘
             │                               │
             └───────────────┬───────────────┘
                             │
                        (BD Check)
                             │
             ┌───────────────┴───────────────┐
             │                               │
         ADMIN?                          ALUNO?
    (role='admin')                 (role='aluno')
             │                               │
             ▼                               ▼
    ┌──────────────────────┐    ┌──────────────────────┐
    │ DASHBOARD ADMIN      │    │ DASHBOARD ALUNO      │
    │ (/dashboard.php)     │    │ (/student/dash.php)  │
    │ - Menu Admin         │    │ - Progresso          │
    │ - Estatísticas       │    │ - Próximas aulas     │
    │ - Quick Links        │    │ - Quick Links        │
    └──────────┬───────────┘    └──────────┬───────────┘
               │                           │
    ┌──────────┴──────────┐    ┌──────────┴──────────┐
    │                     │    │                     │
    ▼                     ▼    ▼                     ▼
┌─────────────┐  ┌──────────────┐  ┌────────────┐  ┌──────────────┐
│ Users       │  │ Courses      │  │ Courses    │  │ My Lessons   │
│ (/admin/    │  │ (/admin/     │  │ (/student/ │  │ (Dashboard)  │
│  users.php) │  │  courses.php)│  │ courses.php│  │              │
└──────┬──────┘  └──────┬───────┘  └──────┬─────┘  └──────────────┘
       │                │                 │
       │         ┌──────┴────────┐        │
       │         │               │        │
       ▼         ▼               ▼        ▼
   ┌────────┐ ┌──────┐  ┌───────────────┐
   │ Approve│ │Modules│  │ Course Detail │
   │ Users  │ │(/admin/   │ (/student/    │
   └────────┘ │modules│   │  course.php)  │
              │.php) │   │               │
              │      │   │   - Módulos   │
              │  ┌───┴──┐│   - Aulas     │
              │  │      │└──────┬────────┘
              └──┘      │       │
                        ▼       ▼
                     ┌──────────────────────┐
                     │ Lessons              │
                     │ (/admin/lessons.php) │
                     │ - CRUD Aulas         │
                     │ - Editor HTML        │
                     └──────────┬───────────┘
                                │
                                ▼
                     ┌──────────────────────┐
                     │ View Lesson          │
                     │ (/student/lesson.php)│
                     │ - Conteúdo HTML      │
                     │ - Materiais          │
                     │ - Comentários        │
                     │ - Marcar Completo    │
                     └──────┬───────────────┘
                            │
                    ┌───────┴───────┐
                    │               │
                    ▼               ▼
              ┌─────────────┐  ┌──────────────┐
              │ Materials   │  │ Logout       │
              │ (/admin/    │  │ (/logout.php)│
              │ material-   │  │              │
              │ upload.php) │  └────────┬─────┘
              └─────────────┘           │
                                        ▼
                                   ┌──────────┐
                                   │ Login    │
                                   │ Page     │
                                   └──────────┘
```

---

## 📱 PÁGINAS ANÔNIMAS (Sem Login)

### 1. `/index.php` (HOME)
```
├── Navbar
│   ├── Logo NR1 EAD
│   ├── Link: Login (/login.php)
│   └── Link: Registrar (/register.php)
├── Hero Section
│   ├── Título: "Aprenda Online"
│   ├── Botão: "Entrar" → /login.php
│   └── Botão: "Registrar" → /register.php
├── Features
│   ├── Feature 1
│   ├── Feature 2
│   └── Feature 3
└── Footer
    ├── Sobre
    ├── Contato
    └── Links úteis
```

### 2. `/login.php` (AUTENTICAÇÃO)
```
├── Navbar
│   ├── Logo → /
│   └── Link: Registrar → /register.php
├── Login Form
│   ├── Input: Email
│   ├── Input: Senha
│   └── Botão: Entrar
├── Links
│   ├── "Não tem conta?" → /register.php
│   └── "Voltar para início" → /
└── Footer
```

### 3. `/register.php` (NOVO USUÁRIO)
```
├── Navbar
│   ├── Logo → /
│   └── Link: Login → /login.php
├── Register Form
│   ├── Input: Nome
│   ├── Input: Email
│   ├── Input: Senha
│   └── Botão: Registrar
├── Links
│   ├── "Já tem conta?" → /login.php
│   └── "Voltar para início" → /
└── Footer
```

---

## 🔐 PÁGINAS AUTENTICADAS (Com Login)

### ADMIN → `/dashboard.php` (ADMIN DASHBOARD)
```
├── Navbar
│   ├── Logo "NR1 EAD" → /
│   ├── "Bem-vindo, Admin"
│   └── Botão: Sair → /logout.php
├── Sidebar Menu (Sticky)
│   ├── Dashboard (/dashboard.php) [ATIVO]
│   └── Voltar ao Site (/)
├── Admin Menu
│   ├── 👥 Usuários → /admin/users.php
│   ├── 📚 Cursos → /admin/courses.php
│   ├── 📋 Módulos → /admin/modules.php
│   ├── 📝 Aulas → /admin/lessons.php
│   └── 📎 Materiais → /admin/material-upload.php
├── Cards (Quick Actions)
│   ├── Total de Usuários
│   ├── Total de Cursos
│   ├── Total de Aulas
│   └── Últimos Usuários
└── Footer
```

### ALUNO → `/student/dashboard.php` (ALUNO DASHBOARD)
```
├── Navbar
│   ├── Logo "NR1 EAD" → /
│   ├── "Bem-vindo, João"
│   └── Botão: Sair → /logout.php
├── Sidebar Menu (Sticky)
│   ├── Dashboard (/student/dashboard.php) [ATIVO]
│   ├── Explorar Cursos (/student/courses.php)
│   └── Voltar ao Site (/)
├── Progress Section
│   ├── Aulas Completas
│   ├── Progresso Geral %
│   └── Últimas Aulas Concluídas
├── My Courses Section
│   ├── Curso 1 (Card)
│   │   ├── Título
│   │   ├── Progresso %
│   │   ├── Próxima Aula
│   │   └── Botão: Continuar → /student/course.php?id=1
│   ├── Curso 2 (Card)
│   └── ...
└── Footer
```

---

## 👥 ADMIN PAGES

### 1. `/admin/users.php` (GERENCIAR USUÁRIOS)
```
├── Navbar (Admin)
│   ├── Logo "NR1 EAD Admin" → /
│   ├── Dashboard → /dashboard.php
│   ├── Voltar ao Site → /
│   └── Sair → /logout.php
├── Sidebar (Admin Menu)
│   ├── Usuários [ATIVO]
│   ├── Cursos → /admin/courses.php
│   ├── Módulos → /admin/modules.php
│   ├── Aulas → /admin/lessons.php
│   └── Materiais → /admin/material-upload.php
├── Content
│   ├── Título: "Gerenciar Usuários"
│   ├── Abas: "Pendentes" / "Aprovados"
│   ├── Usuários Pendentes
│   │   ├── Tabela
│   │   │   ├── Nome
│   │   │   ├── Email
│   │   │   ├── Data
│   │   │   └── Botões: Aprovar / Rejeitar
│   │   └── (Se vazio: "Sem usuários pendentes")
│   └── Usuários Aprovados
│       ├── Tabela
│       │   ├── Nome
│       │   ├── Email
│       │   ├── Role (Admin/Aluno)
│       │   ├── Data
│       │   └── Botões: Promover / Desativar
│       └── (Se vazio: "Sem usuários")
└── Footer
```

### 2. `/admin/courses.php` (GERENCIAR CURSOS)
```
├── Navbar (Admin)
├── Sidebar (Admin Menu)
│   ├── Usuários → /admin/users.php
│   ├── Cursos [ATIVO]
│   ├── Módulos → /admin/modules.php
│   ├── Aulas → /admin/lessons.php
│   └── Materiais → /admin/material-upload.php
├── Content
│   ├── Título: "Gerenciar Cursos"
│   ├── Botão: "+ Novo Curso"
│   ├── Form (Se criando/editando)
│   │   ├── Input: Título
│   │   ├── Input: Descrição
│   │   ├── Input: Instrutor
│   │   └── Botões: Salvar / Cancelar
│   └── Cursos Grid (Cards)
│       ├── Curso 1
│       │   ├── Título
│       │   ├── Descrição (truncada)
│       │   ├── Instrutor
│       │   └── Botões: Editar / Módulos → /admin/modules.php?curso_id=1 / Deletar
│       └── Curso 2
│           └── ...
└── Footer
```

### 3. `/admin/modules.php` (GERENCIAR MÓDULOS)
```
├── Navbar (Admin)
├── Sidebar (Admin Menu)
│   ├── Usuários → /admin/users.php
│   ├── Cursos → /admin/courses.php
│   ├── Módulos [ATIVO]
│   ├── Aulas → /admin/lessons.php
│   └── Materiais → /admin/material-upload.php
├── Content
│   ├── Título: "Gerenciar Módulos"
│   ├── Selector: Escolher Curso
│   ├── Botão: "+ Novo Módulo"
│   ├── Form (Se criando/editando)
│   │   ├── Input: Título
│   │   ├── Input: Descrição
│   │   ├── Input: Ordem
│   │   └── Botões: Salvar / Cancelar
│   └── Módulos Lista
│       ├── Módulo 1
│       │   ├── Título
│       │   ├── Descrição
│       │   ├── Ordem
│       │   ├── Aulas (contador)
│       │   └── Botões: Editar / Aulas → /admin/lessons.php?modulo_id=1 / Deletar
│       └── Módulo 2
│           └── ...
└── Footer
```

### 4. `/admin/lessons.php` (GERENCIAR AULAS)
```
├── Navbar (Admin)
├── Sidebar (Admin Menu)
│   ├── Usuários → /admin/users.php
│   ├── Cursos → /admin/courses.php
│   ├── Módulos → /admin/modules.php
│   ├── Aulas [ATIVO]
│   └── Materiais → /admin/material-upload.php
├── Content
│   ├── Título: "Gerenciar Aulas"
│   ├── Selector: Escolher Módulo
│   ├── Botão: "+ Nova Aula"
│   ├── Form (Se criando/editando)
│   │   ├── Input: Título
│   │   ├── Editor HTML: Conteúdo
│   │   ├── Input: Ordem
│   │   └── Botões: Salvar / Cancelar
│   └── Aulas Lista
│       ├── Aula 1
│       │   ├── Título
│       │   ├── Preview (primeiras linhas)
│       │   ├── Ordem
│       │   └── Botões: Editar / Deletar
│       └── Aula 2
│           └── ...
└── Footer
```

### 5. `/admin/material-upload.php` (UPLOAD DE MATERIAIS)
```
├── Navbar (Admin)
├── Sidebar (Admin Menu)
│   ├── Usuários → /admin/users.php
│   ├── Cursos → /admin/courses.php
│   ├── Módulos → /admin/modules.php
│   ├── Aulas → /admin/lessons.php
│   └── Materiais [ATIVO]
├── Content
│   ├── Título: "Upload de Materiais"
│   ├── Selector: Escolher Aula
│   ├── Form
│   │   ├── Input: Título do Material
│   │   ├── Input File: Selecionar PDF
│   │   └── Botão: Upload
│   └── Materiais Lista
│       ├── Material 1
│       │   ├── Título
│       │   ├── Arquivo
│       │   ├── Data
│       │   └── Botões: Download / Deletar
│       └── Material 2
│           └── ...
└── Footer
```

---

## 👨‍🎓 ALUNO PAGES

### 1. `/student/courses.php` (EXPLORAR CURSOS)
```
├── Navbar (Student)
│   ├── Logo → /
│   ├── "Bem-vindo, João"
│   └── Sair → /logout.php
├── Sidebar Menu
│   ├── Dashboard → /student/dashboard.php
│   ├── Explorar Cursos [ATIVO]
│   └── Voltar ao Site → /
├── Content
│   ├── Título: "Explorar Cursos"
│   ├── Filter: Por Status (Todos / Inscritos / Não Inscritos)
│   └── Cursos Grid (Cards)
│       ├── Curso 1
│       │   ├── Título
│       │   ├── Descrição
│       │   ├── Instrutor
│       │   ├── Módulos (contador)
│       │   ├── Status: "✓ Inscrito" ou "Inscrever"
│       │   └── Botão: Ver Curso → /student/course.php?id=1
│       └── Curso 2
│           └── ...
└── Footer
```

### 2. `/student/course.php?id=X` (DETALHE DO CURSO)
```
├── Navbar (Student)
├── Sidebar Menu
│   ├── Dashboard → /student/dashboard.php
│   ├── Explorar Cursos → /student/courses.php
│   └── Voltar ao Site → /
├── Content
│   ├── Breadcrumb: Dashboard / Cursos / "Título do Curso"
│   ├── Course Header
│   │   ├── Título
│   │   ├── Descrição
│   │   ├── Progresso Overall %
│   │   └── Botão: Inscrever (se não inscrito)
│   ├── Módulos
│   │   ├── Módulo 1
│   │   │   ├── Título
│   │   │   ├── Descrição
│   │   │   ├── Aulas
│   │   │   │   ├── Aula 1
│   │   │   │   │   ├── ✓ (se completa)
│   │   │   │   │   ├── Título
│   │   │   │   │   └── Botão: Ver → /student/lesson.php?id=1
│   │   │   │   └── Aula 2
│   │   │   │       └── ...
│   │   │   └── Progresso: X/Y aulas
│   │   └── Módulo 2
│   │       └── ...
│   └── Navigation
│       ├── Botão: Voltar → /student/courses.php
│       └── Botão: Dashboard → /student/dashboard.php
└── Footer
```

### 3. `/student/lesson.php?id=X` (VISUALIZAR AULA)
```
├── Navbar (Student)
├── Sidebar Menu
├── Content
│   ├── Breadcrumb: Dashboard / Curso / Módulo / "Aula Atual"
│   ├── Lesson Header
│   │   ├── Título da Aula
│   │   └── Status: "Completa" (se marcada) ou "Incompleta"
│   ├── Lesson Content
│   │   ├── HTML Content (renderizado)
│   │   ├── Paragrafos com images
│   │   ├── Code blocks
│   │   └── Formatação preservada
│   ├── Materials Section
│   │   ├── Título: "📎 Materiais"
│   │   ├── Material 1
│   │   │   ├── Ícone PDF
│   │   │   ├── Título
│   │   │   ├── Data
│   │   │   └── Link Download → /uploads/materiais/arquivo.pdf
│   │   └── Material 2
│   │       └── ...
│   ├── Complete Lesson Button
│   │   └── "✓ Marcar Como Completa" → POST
│   ├── Navigation
│   │   ├── "← Aula Anterior" → /student/lesson.php?id=X-1
│   │   └── "Próxima Aula →" → /student/lesson.php?id=X+1
│   ├── Comments Section
│   │   ├── Título: "💬 Comentários (3)"
│   │   ├── Form
│   │   │   ├── Textarea: "Deixe seu comentário..."
│   │   │   └── Botão: Enviar → POST
│   │   └── Comments List
│   │       ├── Comentário 1
│   │       │   ├── Autor: "João Silva"
│   │       │   ├── Data: "10/01/2024 14:30"
│   │       │   └── Texto: "Ótima aula!"
│   │       └── Comentário 2
│   │           └── ...
│   └── Navigation Final
│       ├── "← Voltar para Curso" → /student/course.php?id=X
│       └── "Dashboard" → /student/dashboard.php
└── Footer
```

---

## 🔄 FLUXOS DE AÇÃO

### Fluxo: Registrar Novo Aluno
```
/register.php (form)
    ↓ (POST)
Validação (auth.php:register)
    ↓
BD: Insert into usuarios
    ↓
Mensagem: "Aguardando aprovação"
    ↓ (Admin aprova)
BD: Update usuarios set ativo=1
    ↓ (Aluno faz login)
/login.php
    ↓
/student/dashboard.php
```

### Fluxo: Admin Criar Curso
```
/admin/courses.php (form)
    ↓ (POST)
BD: Insert into cursos
    ↓
Sucesso: "Curso criado"
    ↓
/admin/modules.php?curso_id=X
    ↓ (Criar módulo)
BD: Insert into modulos
    ↓
/admin/lessons.php?modulo_id=X
    ↓ (Criar aula)
BD: Insert into aulas
    ↓
/admin/material-upload.php?aula_id=X
    ↓ (Upload PDF)
BD: Insert into materiais
```

### Fluxo: Aluno Fazer Curso
```
/student/courses.php
    ↓ (Clica em curso)
/student/course.php?id=X
    ↓ (Vê módulos/aulas)
/student/lesson.php?id=X
    ↓ (Lê aula, faz comentário)
Form: Comentário + POST
    ↓
BD: Insert into comentarios
    ↓
Form: Marcar Completa + POST
    ↓
BD: Insert into progresso (ou Update)
    ↓
Dashboard atualiza (%), próxima aula aparece
```

---

## ✅ VERIFICAÇÃO FINAL

- [ ] Todas as 11 páginas `.php` linkadas corretamente
- [ ] Todos os `header('Location: ...)` apontam para URLs válidas
- [ ] Todos os `href="/..."` funcionam com BASE_PATH
- [ ] Botões de Logout em todas as páginas
- [ ] Menu lateral (Sidebar) em todas as páginas autenticadas
- [ ] Breadcrumb em páginas de detalhe
- [ ] Forms com método POST (não GET)
- [ ] Redirects após ações (criar, editar, deletar)
- [ ] Mensagens de sucesso/erro visíveis
- [ ] Permissões verificadas (admin vs aluno)

---

**Mapa criado**: 24/12/2025
**Status**: ✅ Todas as páginas linkadas
**Próximo**: Fazer upload na hospedagem
