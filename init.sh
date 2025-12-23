#!/bin/bash

# Script de inicialização da plataforma NR1 EAD
# Uso: bash init.sh

set -e

echo "🚀 Inicializando NR1 EAD Platform..."
echo ""

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função para imprimir com cor
print_success() {
  echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
  echo -e "${RED}❌ $1${NC}"
}

print_info() {
  echo -e "${YELLOW}ℹ️  $1${NC}"
}

# Verificar se Node.js está instalado
print_info "Verificando Node.js..."
if ! command -v node &> /dev/null; then
  print_error "Node.js não encontrado. Por favor, instale Node.js 16+"
  exit 1
fi
NODE_VERSION=$(node -v)
print_success "Node.js $NODE_VERSION encontrado"
echo ""

# Verificar se MySQL está rodando
print_info "Verificando MySQL..."
if ! command -v mysql &> /dev/null; then
  print_error "MySQL não encontrado. Por favor, instale MySQL 8.0+"
  exit 1
fi
print_success "MySQL encontrado"
echo ""

# Setup Backend
print_info "Configurando Backend..."
cd backend

if [ ! -f .env ]; then
  print_info "Criando .env do backend..."
  cp .env.example .env
  print_success ".env criado (edite com suas credenciais MySQL)"
fi

if [ ! -d node_modules ]; then
  print_info "Instalando dependências do backend..."
  npm install --silent
  print_success "Dependências instaladas"
else
  print_success "Dependências do backend já instaladas"
fi

cd ..
echo ""

# Setup Frontend
print_info "Configurando Frontend..."
cd frontend

if [ ! -d node_modules ]; then
  print_info "Instalando dependências do frontend..."
  npm install --silent
  print_success "Dependências instaladas"
else
  print_success "Dependências do frontend já instaladas"
fi

cd ..
echo ""

# Resumo
print_success "Setup inicial concluído!"
echo ""
print_info "Próximos passos:"
echo "  1. Edite o arquivo: backend/.env com suas credenciais MySQL"
echo "  2. Execute as migrações: cd backend && npm run migrate"
echo "  3. Inicie o backend: cd backend && npm run dev"
echo "  4. Inicie o frontend: cd frontend && npm run dev"
echo ""
print_info "URLs:"
echo "  Frontend: http://localhost:5173"
echo "  Backend: http://localhost:5000"
echo "  API: http://localhost:5000/api"
echo ""
print_info "Credenciais padrão:"
echo "  Email: admin@nr1.com"
echo "  Senha: Admin@123456"
echo ""
