# 🗄️ Como Adicionar Banco de Dados no phpMyAdmin - Hostinger

## 📋 Pré-requisitos

- Conta ativa na Hostinger
- Acesso ao painel de controle (hPanel)
- cpanel/phpMyAdmin disponível

---

## 🚀 MÉTODO 1: Via Painel Hostinger (Recomendado)

### Passo 1: Acessar o Painel
1. Acesse **hpanel.hostinger.com**
2. Faça login com suas credenciais
3. Selecione seu domínio/site

### Passo 2: Ir para Banco de Dados
1. No menu esquerdo, procure por **"Banco de Dados"** ou **"Databases"**
2. Clique em **"Novo Banco de Dados"** ou **"Create Database"**

### Passo 3: Criar Banco de Dados
```
Nome do Banco: nr1_ead
Prefixo (opcional): deixe vazio ou use "nr1_"
```

**Clique em "Criar" ou "Create"**

### Passo 4: Criar Usuário MySQL
1. Na mesma seção, vá para **"Usuários MySQL"** ou **"MySQL Users"**
2. Clique em **"Novo Usuário"** ou **"Create New User"**

```
Nome do Usuário: nr1_user (ou similar)
Senha: Digite uma senha segura
Confirme a Senha
```

**Clique em "Criar Usuário" ou "Create"**

### Passo 5: Associar Usuário ao Banco
1. Ainda em **"Usuários MySQL"**
2. Procure pelo usuário criado
3. Clique em **"Gerenciar Privilégios"** ou **"Manage Privileges"**
4. Selecione o banco `nr1_ead`
5. Marque **"Todos"** ou **"All Privileges"**
6. Clique em **"Aplicar"** ou **"Apply"**

---

## 🗂️ MÉTODO 2: Via phpMyAdmin

### Passo 1: Acessar phpMyAdmin
1. No painel Hostinger, procure por **"phpMyAdmin"**
2. Clique para abrir (abre em nova aba)
3. Faça login como **root** ou usuário admin

### Passo 2: Criar Novo Banco de Dados
1. Clique na aba **"Bancos de Dados"** (Database)
2. Na seção **"Criar novo banco de dados"**, digite: `nr1_ead`
3. Selecione **Collation**: `utf8mb4_unicode_ci`
4. Clique em **"Criar"** ou **"Create"**

### Passo 3: Importar Estrutura do Banco

#### 3.1 - Selecione o banco criado
- Clique em `nr1_ead` na lista esquerda

#### 3.2 - Importar arquivo SQL
1. Clique na aba **"Importar"** ou **"Import"**
2. Em **"Escolher arquivo"**, selecione: `backend/create-db.sql`
3. Deixe as opções padrão
4. Clique em **"Executar"** ou **"Go"**

**Pronto! As tabelas foram criadas automaticamente.**

#### 3.3 - Verificar tabelas criadas
1. Clique no banco `nr1_ead`
2. Na coluna esquerda, você verá as 7 tabelas:
   - ✅ usuarios
   - ✅ cursos
   - ✅ modulos
   - ✅ aulas
   - ✅ materiais
   - ✅ acessos
   - ✅ progresso

---

## 🔐 MÉTODO 3: Via SSH (Linha de Comando)

Se você tiver acesso SSH:

```bash
# 1. Conectar ao MySQL
mysql -u seu_usuario -p

# 2. Criar banco de dados
CREATE DATABASE nr1_ead CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 3. Criar usuário (opcional, se não tiver criado)
CREATE USER 'nr1_user'@'localhost' IDENTIFIED BY 'sua_senha_segura';

# 4. Dar privilégios
GRANT ALL PRIVILEGES ON nr1_ead.* TO 'nr1_user'@'localhost';
FLUSH PRIVILEGES;

# 5. Sair
EXIT;

# 6. Importar estrutura
mysql -u nr1_user -p nr1_ead < backend/create-db.sql
```

---

## ✅ VERIFICAÇÃO PASSO A PASSO

### Após criar o banco, verifique:

#### 1️⃣ No phpMyAdmin
```
Banco de Dados: nr1_ead
    ├─ Tabelas: 7 tabelas
    ├─ usuarios (com admin@nr1.com)
    ├─ cursos (vazio)
    ├─ modulos (vazio)
    ├─ aulas (vazio)
    ├─ materiais (vazio)
    ├─ acessos (vazio)
    └─ progresso (vazio)
```

#### 2️⃣ Verificar usuário admin
1. Clique no banco `nr1_ead`
2. Clique na tabela `usuarios`
3. Você deve ver **1 registro** com:
   - Email: `admin@nr1.com`
   - Nome: `Admin NR1`
   - Senha: (hash bcryptjs)

#### 3️⃣ Testar conexão no backend
Edite `backend/.env`:
```env
DB_HOST=localhost
DB_USER=nr1_user
DB_PASSWORD=sua_senha_segura
DB_NAME=nr1_ead
```

Teste a conexão:
```bash
cd backend
npm install mysql2
node -e "
const mysql = require('mysql2/promise');
const pool = mysql.createPool({
  host: 'localhost',
  user: 'nr1_user',
  password: 'sua_senha',
  database: 'nr1_ead'
});
pool.getConnection().then(conn => {
  console.log('✅ Conexão OK!');
  conn.release();
  process.exit(0);
}).catch(err => {
  console.log('❌ Erro:', err.message);
  process.exit(1);
});
"
```

---

## 📊 CREDENCIAIS A SALVAR

Após completar, guarde essas informações:

```
BANCO DE DADOS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Nome: nr1_ead
Host: localhost
Porta: 3306

USUÁRIO MySQL
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Usuário: nr1_user
Senha: [sua_senha_segura]
Privilégios: ALL

USUÁRIO ADMIN (aplicação)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Email: admin@nr1.com
Senha: 123456
Role: admin
```

---

## 🆘 TROUBLESHOOTING

### ❌ Problema: "Access Denied for user"
**Solução:**
1. Verifique username/password em `.env`
2. No Hostinger, recrie o usuário MySQL
3. Certifique-se de ter privilégios no banco

### ❌ Problema: "Database doesn't exist"
**Solução:**
1. Verifique o nome do banco em `.env`
2. No phpMyAdmin, confirme que `nr1_ead` existe
3. Se não existir, crie manualmente

### ❌ Problema: "No such file or directory"
**Solução:**
1. Certifique-se que `backend/create-db.sql` existe
2. Use o caminho correto ao importar
3. Se via SSH, use: `mysql -u user -p db < /caminho/create-db.sql`

### ❌ Problema: Tabelas não foram criadas
**Solução:**
1. Abra o arquivo `backend/create-db.sql` em texto
2. Verifique se tem conteúdo SQL válido
3. Tente importar novamente no phpMyAdmin
4. Ou execute manualmente via SSH

---

## 🔒 SEGURANÇA

✅ Guarde a senha do usuário MySQL em local seguro  
✅ Use senha forte (mín. 12 caracteres, letras + números + símbolos)  
✅ Não compartilhe credenciais de banco  
✅ Configure backup automático no painel  
✅ Em `.env`, não commitir na git (use `.env.example`)

---

## 📞 SUPORTE HOSTINGER

Se tiver problemas:

**Chat ao vivo:**
- Acesse: hpanel.hostinger.com
- Clique em "Suporte" → "Chat ao vivo"

**Base de Conhecimento:**
- https://www.hostinger.com/help/

**Documentação phpMyAdmin:**
- https://docs.phpmyadmin.net/

---

## ✨ PRÓXIMO PASSO

Após confirmar que o banco está criado e funcionando:

1. ✅ Edite `backend/.env` com credenciais
2. ✅ Teste conexão (veja acima)
3. ✅ Execute `npm start` no backend
4. ✅ Verifique se o servidor inicia
5. ✅ Faça deploy com `deploy.sh`

**Pronto para colocar em produção!** 🚀

---

**Dúvidas?** Consulte DEPLOY_HOSTINGER.md para o próximo passo.
