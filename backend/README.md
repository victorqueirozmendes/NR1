# NR1 EAD - Backend

Backend da plataforma de educação a distância escalável.

## Setup

```bash
npm install
```

## Configurar variáveis de ambiente

```bash
cp .env.example .env
```

Edite o arquivo `.env` com suas credenciais do banco de dados.

## Executar migrações

```bash
npm run migrate
```

## Desenvolvimento

```bash
npm run dev
```

## Produção

```bash
npm start
```

## Endpoints

### Autenticação
- `POST /api/auth/register` - Registro
- `POST /api/auth/login` - Login
- `GET /api/auth/verify` - Verificar token

### Usuários (Admin)
- `POST /api/usuarios` - Criar usuário
- `GET /api/usuarios` - Listar usuários
- `PATCH /api/usuarios/:id/toggle-ativo` - Bloquear/Ativar usuário

### Aluno
- `GET /api/usuarios/me` - Meu perfil

### Cursos
- `POST /api/cursos` - Criar curso (admin)
- `GET /api/cursos/admin/list` - Listar cursos (admin)
- `PATCH /api/cursos/:id` - Editar curso (admin)
- `POST /api/cursos/liberar-acesso` - Liberar curso para aluno (admin)
- `POST /api/cursos/bloquear-acesso` - Bloquear acesso (admin)
- `GET /api/cursos/meus` - Meus cursos (aluno)

### Módulos
- `POST /api/modulos` - Criar módulo (admin)
- `GET /api/modulos/curso/:cursoId` - Listar módulos
- `PATCH /api/modulos/:id` - Editar módulo (admin)
- `DELETE /api/modulos/:id` - Deletar módulo (admin)

### Aulas
- `POST /api/aulas` - Criar aula (admin)
- `GET /api/aulas/modulo/:moduloId` - Listar aulas
- `GET /api/aulas/:id` - Ver aula
- `PATCH /api/aulas/:id` - Editar aula (admin)
- `DELETE /api/aulas/:id` - Deletar aula (admin)

### Materiais
- `POST /api/materiais` - Adicionar material (admin)
- `GET /api/materiais/aula/:aulaId` - Listar materiais
- `DELETE /api/materiais/:id` - Deletar material (admin)

### Progresso
- `POST /api/progresso/marcar-concluida` - Marcar aula como concluída
- `GET /api/progresso/curso/:cursoId` - Meu progresso
- `GET /api/progresso/aluno/:usuarioId/curso/:cursoId` - Progresso de aluno (admin)
