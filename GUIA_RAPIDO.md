# 🎓 NR1 EAD - GUIA RÁPIDO DE USO

## ⚡ Começar em 3 Passos

### 1️⃣ Instalar o Banco
```bash
mysql -u root -p NR1 < database-updates.sql
```

### 2️⃣ Criar Pasta de Uploads
```bash
mkdir -p uploads/materiais
chmod 755 uploads/materiais
```

### 3️⃣ Acessar o Site
```
http://seu-dominio.com
```

---

## 👤 Login Padrão

```
Email: admin@nr1.com
Senha: 123456
```

⚠️ **Altere a senha em produção!**

---

## 🎯 Admin - O Que Fazer

### 📚 Criar um Curso
1. **Dashboard** → **Cursos** → **+ Novo Curso**
2. Preencher: Título, Descrição, Instrutor
3. Salvar

### 📖 Adicionar Módulo
1. **Cursos** → Clicar no curso
2. **+ Novo Módulo**
3. Preencher dados → Salvar

### 📝 Criar Aula com Vídeo YouTube
1. **Aulas** → Selecionar Módulo
2. **+ Nova Aula**
3. Título + Conteúdo
4. **⭐ NOVO**: Colar URL: `https://www.youtube.com/watch?v=VIDEO_ID`
5. Salvar

### 📎 Upload de Materiais
1. **Materiais** → Selecionar Aula
2. Fazer upload do PDF (máx 50MB)
3. Salvar

### 👥 Controlar Acesso de Alunos
1. **⭐ NOVO**: **Acesso de Usuários**
2. Selecionar Curso
3. **Adicionar Aluno**
4. Escolher **Nível**:
   - **Completo** = Vê tudo
   - **Limitado** = Sem materiais
   - **Bloqueado** = Sem acesso
5. Clique em checkbox "Ver Materiais" se quer permitir
6. **Atualizar**

---

## 📱 Aluno - Como Funciona

### Acessar Aula
1. Login com seu email/senha
2. **Meus Cursos** → Selecionar curso
3. Escolher Módulo → Clicar em Aula

### Assistir Vídeo
1. ⭐ **NOVO**: Vídeo aparece no topo da aula
2. Player normal do YouTube
3. Play, pause, volume, tela cheia

### Download de Materiais
1. Scroll down para seção **Materiais**
2. Clicar no PDF
3. Download automático

### Marcar Como Completa
1. Clicar em **✓ Marcar Como Completa**
2. Aula entra na sua lista de concluídas

### Comentar
1. Scroll down para **Comentários**
2. Digitar comentário
3. Enviar

---

## 🎨 Como Customizar Cores

1. Abrir `/css/style-mobile-first.css`
2. Linhas 10-18:
```css
:root {
    --primary: #3498db;      /* ← Altere para sua cor */
    --secondary: #2c3e50;    /* ← Altere para sua cor */
    --success: #27ae60;
    --danger: #e74c3c;
    --light: #f8f9fa;
    --border: #e0e6ed;
    --text: #34495e;
}
```
3. Salvar e recarregar navegador

---

## ⚠️ Problemas Comuns

### Erro: "Access Denied" no Banco
```
Editar: /includes/db.php
Linhas 4-6:
- Usuário correto?
- Senha correta?
- Banco "NR1" existe?
```

### YouTube Não Aparece
```
1. Executou database-updates.sql?
2. URL está correta?
3. Reload da página? (Ctrl+Shift+Del)
```

### Controle de Acesso Não Funciona
```
1. Tabela "user_access_control" existe?
   mysql> SHOW TABLES;
   
2. Se não existir, execute:
   mysql -u root -p NR1 < database-updates.sql
```

### Erro no Upload de Materiais
```
1. Pasta exists? mkdir -p uploads/materiais
2. Permissão? chmod 755 uploads/materiais
3. Arquivo é PDF? Apenas PDF é permitido
4. Máximo 50MB?
```

---

## 📊 Consultas Úteis de Banco

### Ver todos os usuários
```sql
SELECT id, nome, email, role, ativo FROM usuarios;
```

### Ver cursos criados
```sql
SELECT id, titulo, instrutor, created_at FROM cursos;
```

### Ver progresso dos alunos
```sql
SELECT 
    u.nome as aluno,
    a.titulo as aula,
    p.completado,
    p.data_conclusao
FROM progresso p
JOIN usuarios u ON p.usuario_id = u.id
JOIN aulas a ON p.aula_id = a.id
ORDER BY u.nome;
```

### Ver aulas com YouTube
```sql
SELECT id, titulo, youtube_url FROM aulas WHERE youtube_url IS NOT NULL;
```

### Ver acesso de usuários
```sql
SELECT 
    u.nome,
    c.titulo,
    uac.access_level,
    uac.pode_ver_materiais
FROM user_access_control uac
JOIN usuarios u ON uac.usuario_id = u.id
JOIN cursos c ON uac.curso_id = c.id;
```

---

## 🔐 Segurança - O Que Fazer

### ✅ Já Implementado
- ✅ Senhas criptografadas (bcrypt)
- ✅ Prepared statements (anti SQL injection)
- ✅ Validação de input
- ✅ Controle de acesso por role

### 🔧 Você Deve Fazer
- [ ] Mudar senha do admin
- [ ] Ativar HTTPS no servidor
- [ ] Desativar erros do PHP (`display_errors = Off`)
- [ ] Fazer backup semanal do banco
- [ ] Usar senha forte (min 12 caracteres)
- [ ] Mantém plugins atualizados

---

## 📱 Testar em Mobile

### Chrome DevTools
1. Abrir site
2. Pressionar `F12`
3. Clicar ícone de celular (canto superior esquerdo)
4. Escolher "iPhone SE" ou "Pixel 5"
5. Testar navegação

### Celular Real
1. Conectar no mesmo WiFi do servidor
2. Acessar: `http://192.168.0.X:porta`
3. Testar completo

---

## 📈 Quando Vir Para Produção

### Antes de Go Live
- [ ] Testou YouTube? ✅
- [ ] Testou acesso por usuário? ✅
- [ ] Testou em mobile? ✅
- [ ] Backup do banco? ✅
- [ ] Senha admin alterada? ✅
- [ ] HTTPS ativado? ✅
- [ ] Emails testados? (se implementar)
- [ ] Domínio DNS configurado? ✅

### Deploy
```bash
# Copiar arquivos
scp -r nr1-ead/ usuario@servidor:/var/www/

# SSH no servidor
ssh usuario@servidor

# Criar banco
mysql -u root -p < /var/www/nr1-ead/database-updates.sql

# Permissões
chmod 755 /var/www/nr1-ead/uploads/materiais

# Acessar
https://seu-dominio.com
```

---

## 📞 Onde Encontrar Ajuda

| Problema | Solução |
|----------|---------|
| Como instalar? | Leia `DOCUMENTACAO.md` |
| Como testar? | Siga `GUIA_TESTES.md` |
| Tenho uma dúvida | Veja `FAQ.md` |
| Onde tudo fica? | Consulte `MAPA_NAVEGACAO.md` |
| Algo não funciona | Veja `GUIA_TESTES.md` (troubleshooting) |

---

## ✨ Features Principais

| Feature | Admin | Aluno | Docs |
|---------|-------|-------|------|
| Login/Registro | ✅ | ✅ | `DOCUMENTACAO.md` |
| Criar Cursos | ✅ | - | `DOCUMENTACAO.md` |
| Aulas com Vídeo 🎥 | ✅ | ✅ | `FAQ.md` |
| Materiais PDF | ✅ | ✅ | `DOCUMENTACAO.md` |
| Comentários | ✅ | ✅ | `DOCUMENTACAO.md` |
| Progresso | - | ✅ | `DOCUMENTACAO.md` |
| Acesso Granular 🔐 | ✅ | - | `FAQ.md` |

---

## 🎯 Roteiro de Uso

```
1. LOGIN como admin
   └─ Email: admin@nr1.com
      Senha: 123456

2. CRIAR CONTEÚDO
   └─ Curso → Módulo → Aula (com YouTube!)

3. ADICIONAR ALUNOS
   └─ Invite ou aprove registro

4. CONTROLAR ACESSO
   └─ Admin → Acesso de Usuários
      └─ Define quem vê o quê

5. ALUNO ACESSA
   └─ Login como aluno
   └─ Vê cursos inscritos
   └─ Assiste vídeos
   └─ Download materiais
   └─ Marca completo

6. ACOMPANHAR PROGRESSO
   └─ Ver analytics no dashboard
```

---

## 🚀 Go Live Checklist

```
[ ] Banco de dados criado
[ ] Pasta /uploads/materiais criada
[ ] Domínio DNS apontando para servidor
[ ] HTTPS configurado
[ ] Senha admin alterada
[ ] Arquivo /includes/db.php atualizado
[ ] Primeiro curso criado
[ ] Primeiro aluno criado
[ ] YouTube testado
[ ] Acesso testado
[ ] Email testado (se tiver)
[ ] Backup feito
[ ] Pronto! 🚀
```

---

**Pronto para começar? Bom aprendizado! 🎓**

Para detalhes, veja `DOCUMENTACAO.md` ou `FAQ.md`
