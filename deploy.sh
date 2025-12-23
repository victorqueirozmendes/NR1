#!/bin/bash

# ========================================
# NR1 EAD - Script de Deploy Automático
# ========================================

echo "🚀 Iniciando Deploy NR1 EAD..."

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função para mensagens
log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
    exit 1
}

log_info() {
    echo -e "${YELLOW}📌 $1${NC}"
}

# 1. Atualizar código
log_info "Passo 1/7: Atualizando código..."
git pull origin main || log_error "Falha ao fazer pull do Git"
log_success "Código atualizado"

# 2. Instalar dependências backend
log_info "Passo 2/7: Instalando dependências do backend..."
cd backend || log_error "Pasta backend não encontrada"
npm install --production || log_error "Falha ao instalar dependências backend"
log_success "Dependências do backend instaladas"

# 3. Instalar dependências frontend
log_info "Passo 3/7: Instalando dependências do frontend..."
cd ../frontend || log_error "Pasta frontend não encontrada"
npm install --production || log_error "Falha ao instalar dependências frontend"
log_success "Dependências do frontend instaladas"

# 4. Build frontend
log_info "Passo 4/7: Compilando frontend..."
npm run build || log_error "Falha ao compilar frontend"
log_success "Frontend compilado com sucesso"

# 5. Verificar .env
log_info "Passo 5/7: Verificando arquivo .env..."
if [ ! -f "../backend/.env" ]; then
    log_error "Arquivo .env não encontrado! Copie .env.example para .env e configure."
fi
log_success "Arquivo .env encontrado"

# 6. Reiniciar aplicação
log_info "Passo 6/7: Reiniciando aplicação..."
cd ../backend || log_error "Pasta backend não encontrada"

# Verificar se PM2 está instalado
if ! command -v pm2 &> /dev/null; then
    log_info "PM2 não encontrado. Instalando globalmente..."
    npm install -g pm2 || log_error "Falha ao instalar PM2"
fi

# Matar instância anterior
pm2 delete nr1-api 2>/dev/null || true

# Iniciar nova instância
pm2 start src/index.js --name "nr1-api" --instances max || log_error "Falha ao iniciar aplicação com PM2"
pm2 save || log_error "Falha ao salvar configuração PM2"

log_success "Aplicação reiniciada"

# 7. Health check
log_info "Passo 7/7: Fazendo health check..."
sleep 3

HEALTH_CHECK=$(curl -s http://localhost:3000/health || echo "{\"status\":\"failed\"}")
if echo "$HEALTH_CHECK" | grep -q "ok"; then
    log_success "Health check passou!"
else
    log_error "Health check falhou! Resposta: $HEALTH_CHECK"
fi

echo ""
echo "╔════════════════════════════════════════════╗"
echo "║  🎉 Deploy Concluído com Sucesso!         ║"
echo "╚════════════════════════════════════════════╝"
echo ""
echo "📊 Status da Aplicação:"
pm2 status
echo ""
echo "🔗 Acesse: https://seu-dominio.com"
echo "📝 Logs: pm2 logs nr1-api"
echo ""
