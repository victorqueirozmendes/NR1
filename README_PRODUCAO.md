# 🚀 NR1 EAD - Pronto para Produção

> Plataforma de Educação à Distância (EAD) escalável e segura

## 📦 O que está incluído

```
NR1/
├── backend/                  # API Node.js + Express
│   ├── src/
│   │   ├── index.js         # Servidor principal (MySQL)
│   │   ├── index-demo.js    # Servidor demo (em memória)
│   │   ├── controllers/     # Lógica de negócio
│   │   ├── routes/          # Rotas da API
│   │   ├── models/          # Modelos de dados
│   │   ├── middleware/      # Autenticação, validação
│   │   └── config/          # Configuração
│   ├── create-db.sql        # Script inicial do banco
│   ├── package.json         # Dependências
│   └── .env.example         # Variáveis de ambiente
│
├── frontend/                 # React + Vite
│   ├── src/
│   │   ├── pages/          # Páginas (Login, Dashboard)
│   │   ├── components/     # Componentes reutilizáveis
│   │   ├── context/        # Context API (Auth)
│   │   ├── api/            # Chamadas HTTP
│   │   └── styles/         # CSS
│   ├── dist/               # Build pronto para produção
│   └── package.json        # Dependências
│
├── DEPLOY_HOSTINGER.md      # Guia completo de deploy
├── DEPLOY_CHECKLIST.md      # Checklist antes de fazer deploy
├── deploy.sh                # Script automático de deploy
├── ecosystem.config.js      # Configuração PM2
└── nginx.conf              # Configuração servidor web
```

## 🎯 Stack Tecnológico

### Backend
- **Node.js** 18+ - Runtime JavaScript
- **Express** 4.x - Framework web
- **MySQL** 5.7+ - Banco de dados relacional
- **JWT** - Autenticação segura
- **Bcryptjs** - Hash de senhas
- **PM2** - Gerenciador de processos

### Frontend
- **React** 18 - UI Library
- **Vite** 5.x - Build tool rápido
- **React Router** - Navegação
- **Axios** - HTTP Client
- **CSS3** - Styling

## 🔐 Funcionalidades de Segurança

- ✅ Autenticação JWT com 7 dias de expiração
- ✅ Senhas criptografadas com Bcryptjs
- ✅ CORS configurável
- ✅ Rate limiting (Express)
- ✅ Validação de entrada
- ✅ Headers de segurança (Helmet)
- ✅ HTTPS/SSL obrigatório em produção
- ✅ Variáveis de ambiente sensíveis isoladas

## 📊 Estrutura do Banco de Dados

```sql
usuarios          -- Alunos e administradores
├─ id, nome, email, senha, role, ativo, created_at

cursos            -- Cursos disponíveis
├─ id, nome, descricao, criado_por, created_at

modulos           -- Módulos dentro de cursos
├─ id, curso_id, nome, ordem, created_at

aulas             -- Aulas dentro de módulos
├─ id, modulo_id, nome, descricao, video_url, ordem, created_at

materiais         -- Materiais de apoio das aulas
├─ id, aula_id, nome, tipo, url, created_at

acessos           -- Controle de acesso aos cursos
├─ id, usuario_id, curso_id, bloqueado, created_at

progresso         -- Rastreamento de progresso do aluno
├─ id, usuario_id, aula_id, completa, created_at
```

## 🚀 Deploy Rápido (3 passos)

### 1. Configurar Ambiente
```bash
cd backend
cp .env.example .env
# Editar .env com suas credenciais MySQL
```

### 2. Inicializar Banco de Dados
```bash
mysql -u root -p nr1_ead < create-db.sql
```

### 3. Deploy Automático
```bash
chmod +x deploy.sh
./deploy.sh
```

## 📝 Credenciais Padrão

| Campo | Valor |
|-------|-------|
| **Email** | `admin@nr1.com` |
| **Senha** | `123456` |
| **Role** | `admin` |

⚠️ **ALTERE APÓS PRIMEIRO LOGIN!**

## 🔌 API Endpoints (27 endpoints)

### Autenticação (3)
- `POST /api/auth/register` - Registrar novo usuário
- `POST /api/auth/login` - Fazer login
- `GET /api/auth/verify` - Verificar token JWT

### Usuários (4)
- `GET /api/usuarios` - Listar todos (admin)
- `POST /api/usuarios` - Criar usuário (admin)
- `GET /api/usuarios/:id` - Detalhe do usuário
- `PATCH /api/usuarios/:id/toggle` - Ativar/desativar

### Cursos (5)
- `POST /api/cursos` - Criar curso
- `GET /api/cursos` - Listar cursos acessíveis
- `POST /api/cursos/:id/acessos` - Dar acesso
- `PATCH /api/cursos/:id/bloquear/:usuario_id` - Bloquear acesso
- `DELETE /api/cursos/:id` - Deletar curso

### Módulos (4)
- `POST /api/modulos` - Criar módulo
- `GET /api/cursos/:id/modulos` - Listar módulos do curso
- `PATCH /api/modulos/:id` - Editar módulo
- `DELETE /api/modulos/:id` - Deletar módulo

### Aulas (5)
- `POST /api/aulas` - Criar aula
- `GET /api/modulos/:id/aulas` - Listar aulas do módulo
- `GET /api/aulas/:id` - Detalhe da aula
- `PATCH /api/aulas/:id` - Editar aula
- `DELETE /api/aulas/:id` - Deletar aula

### Materiais (3)
- `POST /api/materiais` - Adicionar material
- `GET /api/aulas/:id/materiais` - Listar materiais da aula
- `DELETE /api/materiais/:id` - Deletar material

### Progresso (3)
- `POST /api/progresso` - Marcar aula como completa
- `GET /api/cursos/:id/progresso` - Progresso do curso
- `GET /api/usuarios/:id/progresso` - Todas as conclusões

## 🛠️ Variáveis de Ambiente

```env
# Servidor
PORT=3000                                    # Porta Node.js
NODE_ENV=production                          # Ambiente

# Banco de Dados
DB_HOST=localhost                            # Host MySQL
DB_PORT=3306                                 # Porta MySQL
DB_USER=seu_usuario                          # Usuário MySQL
DB_PASSWORD=sua_senha                        # Senha MySQL
DB_NAME=nr1_ead                              # Nome do banco

# JWT
JWT_SECRET=sua_chave_super_secreta           # Segredo para assinar tokens
JWT_EXPIRATION=7d                            # Expiração do token

# CORS
CORS_ORIGIN=https://seu-dominio.com          # Origem permitida
```

## 📊 Performance & Escalabilidade

- ✅ Pool de conexões MySQL (10 conexões)
- ✅ Clustering com PM2 (múltiplas instâncias)
- ✅ Cache de assets estáticos (1 ano)
- ✅ Gzip compression no Nginx
- ✅ Limites de memória (500MB por processo)
- ✅ Restart automático em falhas
- ✅ Load balancing pronto

## 📱 Responsividade

- ✅ Mobile first design
- ✅ Breakpoints para tablet e desktop
- ✅ Touch-friendly interface
- ✅ Otimizado para todos os navegadores

## 🆘 Troubleshooting Rápido

| Problema | Solução |
|----------|---------|
| **Porta em uso** | `pm2 kill && pm2 start src/index.js` |
| **MySQL não conecta** | Verificar credenciais em `.env` |
| **Frontend em branco** | `cd frontend && npm run build` |
| **CORS error** | Configurar `CORS_ORIGIN` corretamente |
| **JWT expirado** | Fazer login novamente |

## 📞 Suporte & Documentação

- 📖 **DEPLOY_HOSTINGER.md** - Guia passo a passo
- ✅ **DEPLOY_CHECKLIST.md** - Antes de fazer deploy
- 🚀 **deploy.sh** - Script automático
- ⚙️ **ecosystem.config.js** - Configuração PM2
- 🌐 **nginx.conf** - Servidor web

## 🎯 Próximos Passos

1. **Ler** `DEPLOY_HOSTINGER.md` completamente
2. **Preparar** servidor com Node.js + MySQL
3. **Executar** `deploy.sh` ou passos manuais
4. **Verificar** com checklist em `DEPLOY_CHECKLIST.md`
5. **Testar** a plataforma completa
6. **Monitorar** com PM2

## 📈 Estatísticas

- **27 endpoints** implementados
- **7 tabelas** de banco de dados
- **4 páginas** React
- **100% CRUD** operacional
- **Pronto para produção**

---

## 📄 Licença

Copyright © 2025 NR1 EAD. Todos os direitos reservados.

---

**Desenvolvido com ❤️ para educação à distância**

🚀 **Pronto para fazer deploy na Hostinger!**
