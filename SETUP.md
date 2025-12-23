# 🚀 Guia de Setup - NR1 EAD Platform

## ⚡ Início Rápido (5 minutos)

### Pré-requisitos
- Node.js 16+ instalado
- MySQL 8.0+ instalado e rodando
- npm ou yarn

### 1️⃣ Configurar Banco de Dados

```bash
# Criar banco de dados (no MySQL)
mysql -u root -p
CREATE DATABASE ead_platform;
EXIT;
```

### 2️⃣ Setup Backend

```bash
cd backend

# Copiar variáveis de ambiente
cp .env.example .env

# Editar .env com suas credenciais MySQL
nano .env
# Ou abra em um editor de sua preferência

# Instalar dependências
npm install

# Criar tabelas e admin padrão
npm run migrate

# Iniciar servidor (desenvolvimento)
npm run dev
```

**Servidor rodando em**: http://localhost:5000
**API Health Check**: http://localhost:5000/health

### 3️⃣ Setup Frontend

```bash
cd frontend

# Instalar dependências
npm install

# Iniciar desenvolvimento
npm run dev
```

**Frontend rodando em**: http://localhost:5173

### 4️⃣ Acessar a Plataforma

1. Abra http://localhost:5173
2. Faça login com:
   - **Email**: admin@nr1.com
   - **Senha**: Admin@123456

3. Você será redirecionado para o painel administrativo

## 📋 Credenciais Padrão

**Admin**
- Email: `admin@nr1.com`
- Senha: `Admin@123456`

## 🎯 Próximos Passos

### Para Alunos
1. Registrar novo aluno em `/register`
2. Fazer login em `/login`
3. Admin libera acesso aos cursos
4. Aluno acessa `/meus-cursos`

### Para Admin
1. Criar cursos em `/admin/cursos`
2. Criar módulos dentro dos cursos
3. Adicionar aulas aos módulos
4. Adicionar materiais às aulas
5. Liberar cursos para alunos

## 🔧 Variáveis de Ambiente Backend

```env
# Server
PORT=5000
NODE_ENV=development

# Database
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=password
DB_NAME=ead_platform
DB_PORT=3306

# JWT
JWT_SECRET=sua-chave-super-secreta-aqui
JWT_EXPIRATION=24h

# CORS
CORS_ORIGIN=http://localhost:5173

# Admin
ADMIN_EMAIL=admin@nr1.com
ADMIN_PASSWORD=Admin@123456
```

## 📊 Estrutura do Banco de Dados

```sql
users (id, nome, email, senha, role, ativo)
courses (id, nome, descricao)
modules (id, course_id, nome, ordem)
lessons (id, module_id, titulo, descricao, video_url, ordem)
materials (id, lesson_id, tipo, titulo, url)
progress (id, user_id, lesson_id, concluida)
course_access (id, user_id, course_id, ativo)
```

## 🐳 Com Docker

```bash
# Iniciar todos os serviços
docker-compose up -d

# Parar serviços
docker-compose down

# Ver logs
docker-compose logs -f backend
docker-compose logs -f frontend
```

## 📝 Comandos Úteis

### Backend
```bash
npm run dev      # Desenvolvimento com auto-reload
npm start        # Produção
npm run migrate  # Criar tabelas
```

### Frontend
```bash
npm run dev      # Desenvolvimento
npm run build    # Build para produção
npm run preview  # Visualizar build
```

## 🐛 Troubleshooting

### Erro de conexão com MySQL
```bash
# Verificar se MySQL está rodando
mysql -u root -p -e "SELECT 1"

# Se erro de acesso, resetar senha
# No Linux/Mac:
mysqladmin -u root password "nova_senha"
```

### Porta 5000 já em uso
```bash
# Encontrar processo
lsof -i :5000

# Matar processo (macOS/Linux)
kill -9 <PID>

# Ou mudar porta no .env
PORT=5001
```

### Porta 5173 já em uso
```bash
# Mudar porta no vite.config.js
# server: { port: 5174 }
```

### Token expirado
```
Faça login novamente para obter um novo token
```

## 📚 Recursos Úteis

- [Documentação da API](docs/API.md)
- [React Router](https://reactrouter.com)
- [Express.js](https://expressjs.com)
- [JWT](https://jwt.io)

## ✅ Checklist Final

- [ ] MySQL rodando
- [ ] Backend instalado e rodando na porta 5000
- [ ] Frontend instalado e rodando na porta 5173
- [ ] Consegue acessar http://localhost:5173
- [ ] Consegue fazer login com admin@nr1.com
- [ ] Consegue ver o painel administrativo

## 🎉 Pronto!

Agora você pode:
- ✅ Criar cursos
- ✅ Criar módulos e aulas
- ✅ Adicionar materiais
- ✅ Gerenciar usuários
- ✅ Liberar acesso aos cursos
- ✅ Acompanhar progresso dos alunos

Bom aprendizado! 📚
