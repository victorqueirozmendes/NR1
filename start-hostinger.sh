#!/bin/bash

# ========================================
# 🚀 INICIO RÁPIDO - NR1 EAD PARA HOSTINGER
# ========================================

echo "╔════════════════════════════════════════════╗"
echo "║    NR1 EAD - Inicialização Rápida         ║"
echo "╚════════════════════════════════════════════╝"
echo ""

# Cores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# 1. Build Frontend
echo -e "${YELLOW}📦 1. Compilando Frontend...${NC}"
cd frontend
npm install --production
npm run build
echo -e "${GREEN}✅ Frontend compilado!${NC}"
cd ..
echo ""

# 2. Instalar Backend
echo -e "${YELLOW}📦 2. Instalando Backend...${NC}"
cd backend
npm install --production
echo -e "${GREEN}✅ Backend preparado!${NC}"
cd ..
echo ""

# 3. Configuração .env
echo -e "${YELLOW}⚙️  3. Checando arquivo .env...${NC}"
if [ ! -f "backend/.env" ]; then
    cp backend/.env.example backend/.env
    echo -e "${YELLOW}⚠️  Arquivo .env criado. Configure as credenciais MySQL!${NC}"
else
    echo -e "${GREEN}✅ Arquivo .env encontrado${NC}"
fi
echo ""

# 4. Informações importantes
echo -e "${GREEN}╔════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║        ✅ PRONTO PARA HOSTINGER!           ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════╝${NC}"
echo ""

echo "📋 PRÓXIMOS PASSOS NA HOSTINGER:"
echo ""
echo "1️⃣  CONFIGURE .env (backend/.env):"
echo "   DB_HOST=localhost"
echo "   DB_USER=seu_usuario_mysql"
echo "   DB_PASSWORD=sua_senha_mysql"
echo "   DB_NAME=nr1_ead"
echo "   JWT_SECRET=sua_chave_secreta"
echo ""

echo "2️⃣  CRIE O BANCO DE DADOS:"
echo "   mysql -u seu_usuario -p < backend/create-db.sql"
echo ""

echo "3️⃣  INICIE COM PM2:"
echo "   cd backend"
echo "   pm2 start src/index.js --name 'nr1-api'"
echo "   pm2 save"
echo ""

echo "4️⃣  CONFIGURE NGINX:"
echo "   Copie o conteúdo de: nginx.conf"
echo "   Para: /etc/nginx/sites-available/seu-dominio"
echo ""

echo "5️⃣  ATIVE HTTPS:"
echo "   sudo certbot --nginx -d seu-dominio.com"
echo ""

echo "📚 DOCUMENTAÇÃO:"
echo "   └─ DEPLOY_HOSTINGER.md     (Guia passo a passo)"
echo "   └─ DEPLOY_CHECKLIST.md     (Checklist antes de deploy)"
echo "   └─ README_PRODUCAO.md      (Documentação completa)"
echo "   └─ deploy.sh               (Script automático)"
echo ""

echo "🔐 CREDENCIAIS PADRÃO:"
echo "   Email:    admin@nr1.com"
echo "   Senha:    123456"
echo ""

echo "🎯 API:"
echo "   Local:     http://localhost:3000/api"
echo "   Produção:  https://seu-dominio.com/api"
echo ""

echo "✨ Pronto para o deploy! Boa sorte! 🚀"
echo ""
