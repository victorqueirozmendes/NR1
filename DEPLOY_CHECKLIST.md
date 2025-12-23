# 📋 CHECKLIST DE DEPLOY - NR1 EAD

## ✅ Antes do Deploy

- [ ] Node.js 18+ instalado no servidor
- [ ] MySQL 5.7+ disponível
- [ ] Git configurado (se usar versionamento)
- [ ] Domínio apontando para servidor
- [ ] Acesso SSH funcionando
- [ ] Backup do banco de dados feito

## 📦 Preparação do Projeto

- [ ] `backend/.env` criado com credenciais corretas
- [ ] `backend/create-db.sql` pronto
- [ ] `frontend/.env` configurado (se necessário)
- [ ] `npm install` executado em `backend/`
- [ ] `npm install` executado em `frontend/`
- [ ] `npm run build` executado em `frontend/`
- [ ] Verificar se `frontend/dist/` foi criado

## 🗄️ Banco de Dados

- [ ] Banco `nr1_ead` criado
- [ ] Tabelas criadas (rodou `create-db.sql`)
- [ ] Usuário admin criado: `admin@nr1.com` / `123456`
- [ ] Permissões MySQL configuradas
- [ ] Backup automático configurado

## 🌐 Servidor Web (Nginx/Apache)

- [ ] SSL/HTTPS ativado
- [ ] Arquivo de configuração copiado
- [ ] DocumentRoot apontando para `frontend/dist/`
- [ ] Proxy para Node.js configurado
- [ ] GZIP compression ativado
- [ ] Headers de segurança adicionados

## ⚙️ Node.js / PM2

- [ ] PM2 instalado globalmente: `npm install -g pm2`
- [ ] Aplicação iniciada com PM2
- [ ] Aplicação salvaguardada no PM2: `pm2 save`
- [ ] Startup configurado: `pm2 startup`
- [ ] Logs checados: `pm2 logs nr1-api`

## 🔒 Segurança

- [ ] Variáveis de ambiente seguras
- [ ] JWT_SECRET alterado
- [ ] DB_PASSWORD não é padrão
- [ ] Firewall configurado
- [ ] Certificado SSL ativo
- [ ] CORS configurado apenas para seu domínio
- [ ] Rate limiting ativo

## 🧪 Testes de Funcionamento

```bash
# 1. Health Check API
curl https://seu-dominio.com/api/health

# 2. Login (deve retornar token)
curl -X POST https://seu-dominio.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@nr1.com","senha":"123456"}'

# 3. Acessar frontend
# Abra https://seu-dominio.com no navegador

# 4. Fazer login no painel
# Acesse o Dashboard Admin

# 5. Testar criação de curso
# Admin > Criar Novo Curso

# 6. Ver logs
pm2 logs nr1-api
```

## 📊 Monitoramento

- [ ] PM2 monitoramento ativo
- [ ] Logs verificados regularmente
- [ ] Alertas de erro configurados
- [ ] Backup automático MySQL ativo
- [ ] Monitoring de CPU/RAM ativo

## 🚀 Go-Live

- [ ] Todos os checkboxes acima marcados
- [ ] Testes de funcionamento completados
- [ ] Usuários informados sobre ativação
- [ ] Suporte disponível para problemas
- [ ] Documentação atualizada

## 📞 Em Caso de Problemas

### Aplicação não inicia
```bash
pm2 logs nr1-api
# Verificar erro nos logs
```

### MySQL não conecta
```bash
mysql -u seu_usuario -p
SHOW DATABASES;
USE nr1_ead;
SHOW TABLES;
```

### Frontend não carrega
```bash
# Verificar se dist/ existe
ls -la frontend/dist/
# Reconstruir se necessário
cd frontend && npm run build
```

### Port 3000 em uso
```bash
lsof -i :3000
kill -9 <PID>
pm2 start src/index.js --name "nr1-api"
```

## 🔗 Links Úteis

- **Painel Hostinger:** https://hpanel.hostinger.com
- **PM2 Docs:** https://pm2.keymetrics.io/
- **Let's Encrypt:** https://letsencrypt.org/
- **Nginx Docs:** https://nginx.org/en/docs/

---

**Boa Sorte no Deploy!** 🎉
