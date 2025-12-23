# NR1 EAD - Frontend

Frontend da plataforma de educação a distância em React + Vite.

## Setup

```bash
npm install
```

## Desenvolvimento

```bash
npm run dev
```

A aplicação estará disponível em `http://localhost:3000`

## Build

```bash
npm run build
```

## Arquitetura

### Estrutura de Pastas

```
src/
├── api/
│   ├── api.js           # Configuração do Axios
│   └── endpoints.js     # Endpoints da API
├── components/
│   └── ProtectedRoute.jsx  # Rota protegida
├── context/
│   └── AuthContext.jsx  # Context de autenticação
├── pages/
│   ├── Login.jsx
│   ├── Register.jsx
│   ├── DashboardAluno.jsx
│   └── DashboardAdmin.jsx
├── styles/
│   ├── auth.css
│   ├── dashboard.css
│   ├── admin.css
│   └── index.css
├── App.jsx
└── main.jsx
```

## Features

- ✅ Login e Registro
- ✅ Dashboard do Aluno
- ✅ Painel Administrativo
- ✅ Controle de Acesso com JWT
- ✅ Integração com API Backend
