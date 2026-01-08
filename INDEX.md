# 📚 Índice de Documentação - NR1 EAD

Bem-vindo à plataforma de educação a distância NR1 EAD! Este documento direciona você para a documentação correta.

---

## 🚀 Começar Rápido

**Se você está iniciando agora:**
1. Leia: `README.md` - Visão geral
2. Siga: `DOCUMENTACAO.md` - Instalação passo-a-passo
3. Teste: `GUIA_TESTES.md` - Validar que tudo funciona

**Tempo estimado**: 30 minutos

---

## 📖 Documentação Disponível

### 1. **README.md** 📘
**O quê**: Visão geral da plataforma
**Quando usar**: Na primeira visita
**Contém**:
- O que é NR1 EAD
- Principais features
- Links rápidos para início

---

### 2. **DOCUMENTACAO.md** 📗
**O quê**: Guia completo de instalação e uso
**Quando usar**: Durante instalação e configuração
**Contém**:
- Pré-requisitos (PHP, MySQL)
- Passo-a-passo de instalação
- Estrutura do projeto
- Esquema do banco de dados
- Como adicionar vídeos YouTube
- Como usar controle de acesso
- Tecnologias utilizadas

---

### 3. **RESUMO_MUDANCAS.md** 📊
**O quê**: O que foi feito nesta sessão
**Quando usar**: Para entender as últimas atualizações
**Contém**:
- ✅ 4 problemas resolvidos
- 🎨 CSS restaurado
- 🎥 YouTube integrado
- 🔐 Acesso por usuário implementado
- 📊 Estatísticas de mudanças
- 🚀 Próximos passos

**Recomendado**: Leia primeiro para entender o que mudou!

---

### 4. **GUIA_TESTES.md** 🧪
**O quê**: Como validar que tudo funciona
**Quando usar**: Depois de instalar
**Contém**:
- Checklist de 6 testes
- Comandos de validação
- Testes de mobile
- Fluxo de teste completo
- Resolução de problemas
- Métricas esperadas

**Importante**: Execute todos os testes antes de ir para produção!

---

### 5. **FAQ.md** ❓
**O quê**: Perguntas frequentes respondidas
**Quando usar**: Quando tem dúvidas
**Contém**:
- Instalação & Setup
- YouTube
- Controle de Acesso
- Materiais (PDF)
- Autenticação
- Performance
- Segurança
- Design & Layout
- Banco de Dados
- Suporte & Desenvolvimento
- Troubleshooting

**Dica**: Use Ctrl+F para buscar sua dúvida!

---

### 6. **MAPA_NAVEGACAO.md** 🗺️
**O quê**: Mapa visual da navegação do site
**Quando usar**: Para entender o fluxo do usuário
**Contém**:
- Fluxo de admin
- Fluxo de aluno
- Páginas disponíveis
- Hierarquia de menus

---

### 7. **AUDITORIA_RESPONSIVO.md** 📱
**O quê**: Detalhes da auditoria de responsividade
**Quando usar**: Se tem problemas de layout
**Contém**:
- Análise de responsividade
- Breakpoints
- Testes de mobile

---

## 🗂️ Estrutura de Arquivos

### Código PHP
```
├── index.php                  # Página inicial
├── login.php                  # Login
├── register.php               # Registro
├── logout.php                 # Logout
├── dashboard.php              # Dashboard
│
├── admin/
│   ├── users.php             # Gerenciar usuários
│   ├── courses.php           # Gerenciar cursos
│   ├── modules.php           # Gerenciar módulos
│   ├── lessons.php           # Gerenciar aulas (com YouTube)
│   ├── material-upload.php   # Upload de PDFs
│   └── access-control.php    # Controle de acesso (NOVO)
│
└── student/
    ├── dashboard.php         # Dashboard aluno
    ├── courses.php           # Meus cursos
    ├── course.php            # Detalhes curso
    └── lesson.php            # Visualizar aula
```

### Arquivos de Sistema
```
├── includes/
│   ├── auth.php              # Autenticação + funções (5 novas)
│   └── db.php                # Conexão banco
│
├── css/
│   └── style-mobile-first.css # Estilos (600+ linhas)
│
├── uploads/
│   └── materiais/            # PDFs dos alunos
│
└── config/
    └── (futuro)
```

### Banco de Dados
```
├── database-init.sql         # Criação inicial (não está aqui)
└── database-updates.sql      # YouTube + Acesso
```

### Documentação
```
├── README.md                 # Visão geral
├── DOCUMENTACAO.md           # Guia completo
├── RESUMO_MUDANCAS.md       # O que foi feito
├── GUIA_TESTES.md           # Como testar
├── FAQ.md                   # Perguntas frequentes
├── MAPA_NAVEGACAO.md        # Fluxo do site
├── AUDITORIA_RESPONSIVO.md  # Análise de layout
└── INDEX.md                 # Este arquivo
```

---

## 🎯 Caminhos Recomendados

### Sou Desenvolvedor e Quero Instalar
1. `DOCUMENTACAO.md` → Instalação
2. `database-updates.sql` → Executar no banco
3. `GUIA_TESTES.md` → Validar instalação

### Sou Admin e Quero Usar a Plataforma
1. `DOCUMENTACAO.md` → Entender features
2. `FAQ.md` → Acesso por Usuário
3. `MAPA_NAVEGACAO.md` → Onde clicar

### Tenho um Problema
1. `FAQ.md` → Buscar por palavra-chave
2. `GUIA_TESTES.md` → Seção "Resolução de Problemas"
3. Abrir GitHub Issue

### Quero Customizar o Design
1. `FAQ.md` → Seção "Design & Layout"
2. `css/style-mobile-first.css` → Editar cores/estilos
3. `RESUMO_MUDANCAS.md` → Ver mudanças recentes

---

## ✨ Features Principais

| Feature | Arquivo | Docs |
|---------|---------|------|
| **Login/Registro** | `auth.php` | `DOCUMENTACAO.md` |
| **Cursos & Módulos** | `admin/courses.php` | `DOCUMENTACAO.md` |
| **Aulas** | `admin/lessons.php` | `DOCUMENTACAO.md` |
| **Vídeos YouTube** ✨ | `admin/lessons.php` | `FAQ.md` |
| **Materiais PDF** | `admin/material-upload.php` | `DOCUMENTACAO.md` |
| **Progresso Aluno** | `student/lesson.php` | `DOCUMENTACAO.md` |
| **Comentários** | `student/lesson.php` | `DOCUMENTACAO.md` |
| **Acesso por Usuário** ✨ | `admin/access-control.php` | `RESUMO_MUDANCAS.md` |
| **Design Responsivo** | `css/style-mobile-first.css` | `GUIA_TESTES.md` |

---

## 🔄 Fluxo Recomendado de Leitura

```
START
  │
  ├─→ Primeira Vez? → README.md
  │      │
  │      ├─→ Instalar? → DOCUMENTACAO.md
  │      │      │
  │      │      └─→ Validar? → GUIA_TESTES.md
  │      │
  │      └─→ Navegar? → MAPA_NAVEGACAO.md
  │
  ├─→ Tem Dúvida? → FAQ.md
  │
  ├─→ Problema? → GUIA_TESTES.md (Troubleshooting)
  │
  └─→ Quer Customizar? → DOCUMENTACAO.md + CSS
```

---

## 📞 Suporte

**Onde encontrar ajuda:**

1. **Documentação** - 90% das dúvidas
   - FAQ.md (perguntas frequentes)
   - DOCUMENTACAO.md (detalhes técnicos)

2. **Testes** - Validar funcionamento
   - GUIA_TESTES.md (testes unitários)
   - Troubleshooting section

3. **GitHub** - Bugs e sugestões
   - Abrir Issue com detalhes
   - Descrever o problema

4. **Código** - Ler comentários
   - auth.php
   - db.php
   - admin/access-control.php

---

## 🎓 Convenções do Projeto

### Nomenclatura de Banco
- Tabelas: **português, minúsculas** (`usuarios`, `aulas`)
- Colunas: **snake_case** (`usuario_id`, `created_at`)
- Valores enum: **sem acentos, minúsculas** (`completo`, `limitado`)

### Nomenclatura de Código PHP
- Funções: **camelCase** (`podeAcessarCurso()`)
- Variáveis: **camelCase** (`$usuarioId`)
- Constantes: **SNAKE_CASE** (`$_SESSION`)
- Classes: **PascalCase** (não usado, projeto procedural)

### CSS
- Classes: **kebab-case** (`.table-wrapper`, `.btn-primary`)
- IDs: **camelCase** (pouco usado)
- Variáveis CSS: **kebab-case** (`--primary`, `--secondary`)

---

## 🚨 Checklist Pré-Produção

- [ ] Leu DOCUMENTACAO.md?
- [ ] Executou GUIA_TESTES.md?
- [ ] Backup do banco feito?
- [ ] HTTPS configurado?
- [ ] Uploads testados?
- [ ] YouTube testado?
- [ ] Acesso por usuário testado?
- [ ] Senhas fortes criadas?
- [ ] Email admin confirmado?
- [ ] Testou em mobile?

---

## 📊 Estatísticas do Projeto

| Item | Quantidade |
|------|-----------|
| Documentos | 7 |
| Páginas PHP | 13 |
| Tabelas DB | 8 |
| Funções Auth | 16 |
| Linhas CSS | 600+ |
| Features | 9 |
| Páginas Docs | 50+ |

---

## 🎉 Você Está Pronto!

Tudo está documentado e testado. Escolha seu caminho acima e bom desenvolvimento! 🚀

**Próximo passo**: Clique em um dos arquivos de documentação acima.

---

**Última atualização**: 2024
**Versão**: 1.0.0
**Status**: ✅ Produção Pronta

Para dúvidas, consulte `FAQ.md` ou abra um GitHub Issue.
