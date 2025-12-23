#!/bin/bash

echo "🚀 NR1 EAD - Inicialização Automática"
echo "===================================="

# Cores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}1️⃣  Instalando dependências do Backend...${NC}"
cd backend
npm install
echo -e "${GREEN}✅ Backend instalado${NC}\n"

echo -e "${BLUE}2️⃣  Copiando arquivo .env do Backend...${NC}"
if [ ! -f .env ]; then
  cp .env.example .env
  echo -e "${YELLOW}⚠️  Configure o arquivo backend/.env com suas credenciais MySQL${NC}"
else
  echo -e "${GREEN}✅ .env já existe${NC}"
fi
echo ""

echo -e "${BLUE}3️⃣  Instalando dependências do Frontend...${NC}"
cd ../frontend
npm install
echo -e "${GREEN}✅ Frontend instalado${NC}\n"

echo -e "${BLUE}4️⃣  Copiando arquivo .env do Frontend...${NC}"
if [ ! -f .env ]; then
  cp .env.example .env
  echo -e "${GREEN}✅ .env criado${NC}"
else
  echo -e "${GREEN}✅ .env já existe${NC}"
fi
echo ""

echo -e "${GREEN}═══════════════════════════════════════${NC}"
echo -e "${GREEN}✅ Setup concluído com sucesso!${NC}"
echo -e "${GREEN}═══════════════════════════════════════${NC}\n"

echo -e "${BLUE}📋 Próximos passos:${NC}"
echo -e "1. ${YELLOW}Configure o arquivo ${GREEN}backend/.env${YELLOW} com suas credenciais MySQL"
echo -e "2. Execute ${GREEN}npm run migrate${YELLOW} dentro de ${GREEN}backend/${YELLOW} para criar as tabelas"
echo -e "3. Abra 2 terminais:"
echo -e "   - Terminal 1: ${GREEN}cd backend && npm run dev"
echo -e "   - Terminal 2: ${GREEN}cd frontend && npm run dev"
echo -e "4. Acesse ${GREEN}http://localhost:3000${NC}\n"

echo -e "${BLUE}📚 Documentação:${NC}"
echo -e "- QUICKSTART.md   - Início rápido"
echo -e "- SETUP.md        - Guia detalhado"
echo -e "- README.md       - Visão geral completa"
echo -e "- docs/API.md     - Documentação da API"
echo -e "- docs/EXEMPLOS.md - Exemplos de uso\n"

echo -e "${BLUE}💡 Dica: Se tiver problemas, verifique o arquivo TROUBLESHOOTING.md${NC}"
