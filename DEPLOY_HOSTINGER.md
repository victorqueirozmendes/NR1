# NR1 EAD - Guia de Deploy na Hostinger

## 📋 Pré-requisitos

- Node.js 18+ instalado na Hostinger
- MySQL 5.7+ disponível
- Acesso SSH ao servidor
- Domínio configurado

## 🚀 Passos para Deploy

### 1. Preparar o Servidor

```bash
# Conectar via SSH
ssh usuario@seu-dominio.com

# Ir para a pasta public_html ou htdocs
cd ~/public_html

# Ou se preferir em pasta separada:
mkdir -p ~/nr1-ead && cd ~/nr1-ead
```

### 2. Clonar ou Upload do Projeto

```bash
# Opção A: Clone do Git (se estiver em repositório)
git clone seu-repositorio.git .

# Opção B: Upload via FTP/SFTP
# Use FileZilla ou WinSCP para fazer upload dos arquivos
```

### 3. Instalar Dependências

```bash
# Backend
cd backend
npm install --production

# Frontend
cd ../frontend
npm install --production
```

### 4. Build do Frontend

```bash
cd frontend
npm run build
# Isso cria a pasta 'dist' com arquivos estáticos prontos
```

### 5. Configurar Banco de Dados

```bash
# Acessar MySQL
mysql -u seu_usuario -p

# Executar o script de inicialização
mysql -u seu_usuario -p seu_banco < ../backend/create-db.sql
```

### 6. Configurar Variáveis de Ambiente

```bash
cd ~/nr1-ead/backend

# Editar .env com suas credenciais
nano .env
```

**Conteúdo do .env para Hostinger:**
```
DB_HOST=localhost
DB_USER=seu_usuario_mysql
DB_PASSWORD=sua_senha_mysql
DB_NAME=nr1_ead
DB_PORT=3306
JWT_SECRET=sua_chave_super_secreta_aleatorio_aqui
NODE_ENV=production
PORT=3000
CORS_ORIGIN=https://seu-dominio.com
```

### 7. Configurar Nginx/Apache

#### Para Nginx:
```nginx
server {
    listen 80;
    server_name seu-dominio.com;

    # Frontend (React build)
    location / {
        root /home/usuario/nr1-ead/frontend/dist;
        try_files $uri $uri/ /index.html;
    }

    # API Backend
    location /api/ {
        proxy_pass http://localhost:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
    }
}
```

#### Para Apache:
```apache
<VirtualHost *:80>
    ServerName seu-dominio.com
    
    # Frontend
    DocumentRoot /home/usuario/nr1-ead/frontend/dist
    
    # Rewrite para React Router
    <Directory /home/usuario/nr1-ead/frontend/dist>
        RewriteEngine On
        RewriteBase /
        RewriteRule ^index\.html$ - [L]
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule . /index.html [L]
    </Directory>
    
    # Proxy para API
    ProxyPreserveHost On
    ProxyPass /api http://localhost:3000/api
    ProxyPassReverse /api http://localhost:3000/api
</VirtualHost>
```

### 8. Usar PM2 para Gerenciar Processo Node

```bash
# Instalar PM2 globalmente
npm install -g pm2

# Iniciar aplicação
cd ~/nr1-ead/backend
pm2 start src/index.js --name "nr1-api"

# Salvar configuração
pm2 save

# Iniciar no boot
pm2 startup
```

### 9. Ativar HTTPS (SSL)

Hostinger geralmente oferece Let's Encrypt grátis:

```bash
# Via Certbot
sudo certbot --nginx -d seu-dominio.com

# Ou via painel Hostinger
# 1. Vá para Segurança > Certificados SSL
# 2. Ative o SSL grátis
```

### 10. Testar Acesso

```bash
# Backend
curl https://seu-dominio.com/api/health

# Frontend
# Acesse https://seu-dominio.com no navegador
```

## 📊 Monitoramento

```bash
# Ver logs
pm2 logs nr1-api

# Status
pm2 status

# Restart
pm2 restart nr1-api
```

## 🔧 Variáveis de Ambiente Importantes

| Variável | Valor | Descrição |
|----------|-------|-----------|
| `NODE_ENV` | `production` | Modo de produção |
| `PORT` | `3000` | Porta do backend |
| `DB_HOST` | `localhost` | Host do MySQL |
| `JWT_SECRET` | Aleatório | Chave super secreta |
| `CORS_ORIGIN` | `https://seu-dominio.com` | Origem permitida |

## ⚠️ Segurança

- ✅ Ativar HTTPS
- ✅ Usar variáveis de ambiente
- ✅ Manter JWT_SECRET seguro
- ✅ Configurar firewall
- ✅ Backups automáticos do MySQL
- ✅ Monitorar logs

## 🆘 Troubleshooting

**Port 3000 já está em uso:**
```bash
pm2 kill
lsof -i :3000
kill -9 <PID>
```

**Erro de conexão MySQL:**
```bash
mysql -u root -p
SHOW DATABASES;
USE nr1_ead;
SHOW TABLES;
```

**Frontend não carrega:**
```bash
cd frontend
npm run build
# Verificar se dist/ foi criada
ls -la dist/
```

## 📞 Suporte Hostinger

- Chat ao vivo: Painel de controle > Suporte
- Email: support@hostinger.com
- Docs: https://www.hostinger.com/help/

---

**Pronto para deploy!** 🎉
