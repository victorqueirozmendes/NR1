# 🎓 NR1 - Plataforma EAD Escalável

> **Plataforma de Educação a Distância** com autenticação segura, painel administrativo completo e controle de acesso baseado em roles.

## 🚀 Início Rápido

### Para Linux/Mac:
```bash
bash init.sh
cd backend && npm run migrate
npm run dev
# Em outro terminal
cd frontend && npm run dev
```

### Para Windows:
```bash
init.bat
cd backend && npm run migrate
npm run dev
# Em outro terminal
cd frontend && npm run dev
```

## 📋 Credenciais Padrão

- **Email**: admin@nr1.com
- **Senha**: Admin@123456

## 📚 Documentação

- [📖 README.md](README.md) - Visão geral completa
- [🚀 SETUP.md](SETUP.md) - Guia de instalação passo a passo
- [📊 ESTRUTURA.txt](ESTRUTURA.txt) - Estrutura visual do projeto
- [🔌 API.md](docs/API.md) - Documentação completa da API
- [💡 EXEMPLOS.md](docs/EXEMPLOS.md) - Exemplos de requisições

## 🌐 URLs Locais

- **Frontend**: http://localhost:5173
- **Backend**: http://localhost:5000
- **API**: http://localhost:5000/api

## 🛠️ Tech Stack

**Backend**
- Node.js + Express
- MySQL 8.0+
- JWT Authentication
- Bcryptjs

**Frontend**
- React 18
- Vite
- React Router
- Axios

## 🔐 Features

✅ Autenticação com JWT
✅ Roles (Admin, Aluno)
✅ Proteção de rotas
✅ Gerenciamento de cursos
✅ Aulas e módulos
✅ Materiais (PDFs, links)
✅ Rastreamento de progresso
✅ Painel administrativo
✅ Gerenciamento de usuários

## 📦 Estrutura

```
NR1/
├── backend/          # Express + MySQL
├── frontend/         # React + Vite
├── docs/            # Documentação
├── init.sh          # Setup Linux/Mac
├── init.bat         # Setup Windows
└── docker-compose.yml
```

## 🐳 Com Docker

```bash
docker-compose up -d
```

## 📞 Suporte

Veja os documentos de documentação para mais informações.

---

**Desenvolvido com ❤️ para educação de qualidade**
