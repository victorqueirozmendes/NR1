# 🧪 GUIA DE TESTES - NR1 EAD

## ✅ Checklist de Validação

### 1. CSS Design Futurista
- [ ] Página inicial tem navbar com gradiente preto/azul
- [ ] Botões têm gradiente azul e hover com sombra
- [ ] Cards têm sombra e border
- [ ] Texto primário é azul (#3498db)
- [ ] Texto secundário é preto (#2c3e50)
- [ ] Site é responsivo em celular (320px)
- [ ] Site é responsivo em tablet (768px)
- [ ] Site é responsivo em desktop (1024px+)

### 2. Página de Materiais
- [ ] Admin → Materiais abre sem erro
- [ ] Página não mostra tela branca
- [ ] Tabela de materiais aparece corretamente
- [ ] Botão "Enviar Material" funciona
- [ ] Upload de PDF funciona
- [ ] Tabela é responsiva no celular

### 3. Vídeos YouTube (Novo)

#### No Admin
- [ ] Admin → Aulas → Editar aula
- [ ] Campo "URL do Vídeo YouTube" aparece
- [ ] Exemplo mostrado no campo
- [ ] Pode inserir URL do YouTube
- [ ] Salva corretamente no banco

#### No Aluno
- [ ] Aluno vê aula com vídeo
- [ ] Vídeo é exibido em iframe responsivo
- [ ] Vídeo funciona (play, pause, volume)
- [ ] Vídeo não quebra layout no celular
- [ ] Conteúdo da aula fica abaixo do vídeo

### 4. Controle de Acesso (Novo)

#### Admin
- [ ] Menu Admin tem "Acesso de Usuários" (novo)
- [ ] Página abre sem erros
- [ ] Dropdown de cursos funciona
- [ ] Pode adicionar aluno ao curso
- [ ] Dropdown "Nível" muda: Completo/Limitado/Bloqueado
- [ ] Checkbox "Ver Materiais" funciona
- [ ] Botão "Remover" funciona

#### Aluno
- [ ] Aluno com acesso "Completo" vê tudo
- [ ] Aluno com "Limitado" vê conteúdo mas não materiais
- [ ] Aluno com "Bloqueado" não vê o curso
- [ ] Resposta rápida sem latência

### 5. Navbars
- [ ] Navbar em todas as 9 páginas tem layout correto
- [ ] Logo fica à esquerda
- [ ] Menu (usuário + sair) fica à direita
- [ ] Navbar não tem quebra de linha
- [ ] Funciona em mobile

### 6. Responsividade Geral
- [ ] Mobile (320px): Sidebar desaparece, conteúdo ocupa 100%
- [ ] Tablet (768px): Sidebar aparece lado a lado
- [ ] Desktop (1024px): Layout completo
- [ ] Tabelas em mobile veem card layout
- [ ] Botões ocupam 100% em mobile
- [ ] Formulários são acessíveis

---

## 🔧 Comandos de Teste no Terminal

### 1. Verificar Estrutura de Pastas
```bash
cd /home/usuario/Documentos/GitHub/NR1
ls -la
# Deve mostrar: admin/, student/, css/, includes/, uploads/materiais/
```

### 2. Verificar Arquivo CSS
```bash
wc -l css/style-mobile-first.css
# Deve ter 600+ linhas
```

### 3. Verificar Novas Funções em Auth
```bash
grep -n "podeAcessarCurso\|registrarUsuarioNoCurso\|atualizarPermissaoUsuario" includes/auth.php
# Deve mostrar 5 funções novas
```

### 4. Verificar YouTube em Lessons
```bash
grep -n "youtube_url" admin/lessons.php
# Deve mostrar campo no formulário
```

### 5. Verificar Access Control
```bash
ls -la admin/access-control.php
# Deve existir o arquivo
```

---

## 📱 Testes de Mobile

### Com Chrome DevTools
1. Abrir site em `http://localhost`
2. Pressionar `F12` para abrir DevTools
3. Clicar em ícone de celular (Toggle Device Toolbar)
4. Escolher "iPhone SE" (375px)
5. Verificar:
   - [ ] Layout não quebra
   - [ ] Navbar tem altura adequada
   - [ ] Botões são clicáveis
   - [ ] Formulários são usáveis
   - [ ] Tabelas viram cards

### Com Celular Real
1. Conectar no mesmo WiFi
2. Acessar `http://192.168.x.x` (IP do servidor)
3. Testar navegação completa

---

## 🎯 Fluxo de Teste Completo

### 1. Login
```
1. Ir para http://seu-site.com/login.php
2. Email: admin@nr1.com (ou seu admin)
3. Senha: 123456 (ou sua senha)
4. Clicar em "Entrar"
5. ✅ Deve abrir dashboard
```

### 2. Testar YouTube
```
1. Admin → Aulas
2. Selecionar um módulo
3. Editar uma aula existente
4. No campo "URL do Vídeo YouTube", colar:
   https://www.youtube.com/watch?v=dQw4w9WgXcQ
5. Clicar "Atualizar"
6. Logout e ir para http://seu-site.com (como aluno)
7. Cursos → Curso → Módulo → Aula
8. ✅ Deve ver iframe do vídeo
```

### 3. Testar Controle de Acesso
```
1. Admin → Acesso de Usuários
2. Selecionar um curso
3. Adicionar um aluno
4. Clicar em dropdown de nível
5. Escolher "Bloqueado"
6. Logout (como admin)
7. Login como aquele aluno
8. ✅ Aquele curso NÃO deve aparecer
9. Voltar como admin
10. Mudar para "Completo"
11. Login como aluno
12. ✅ Curso agora aparece
```

### 4. Testar Materiais
```
1. Admin → Materiais
2. Selecionar uma aula
3. Enviar um PDF
4. ✅ Material deve aparecer na tabela
5. Ir para aula como aluno
6. ✅ Material deve aparecer com link para download
```

---

## 🐛 Resolução de Problemas

### Tela branca em material-upload.php
**Solução**: Verificar se `/uploads/materiais/` existe
```bash
mkdir -p /home/usuario/Documentos/GitHub/NR1/uploads/materiais
chmod 755 /home/usuario/Documentos/GitHub/NR1/uploads/materiais
```

### YouTube não aparece
**Solução**: Executar script SQL
```bash
mysql -u root -p NR1 < /home/usuario/Documentos/GitHub/NR1/database-updates.sql
```

### Controle de acesso não aparece
**Solução**: Verificar se tabela foi criada
```bash
mysql -u root -p NR1 -e "SHOW TABLES LIKE 'user_access%';"
# Deve retornar: user_access_control
```

### CSS não funciona
**Solução**: Limpar cache do navegador
```
Ctrl+Shift+Del (ou Cmd+Shift+Del no Mac)
Selecionar "Cookies e cache"
Clicar "Limpar dados"
```

---

## 📊 Métricas Esperadas

| Métrica | Valor |
|---------|-------|
| Tempo de Carregamento | < 2s |
| Tamanho da Página | < 500KB |
| Requisições | < 30 |
| CSS Size | ~30KB |
| Mobile Score (Lighthouse) | > 90 |
| Acessibilidade | > 85 |

---

## ✅ Checklist Final

- [ ] Todos os 6 testes de CSS passam
- [ ] Material upload funciona
- [ ] YouTube plays em aulas
- [ ] Controle de acesso funciona
- [ ] Navbars têm layout correto
- [ ] Responsividade funciona
- [ ] Testes de mobile funcionam
- [ ] Fluxo completo OK
- [ ] Sem erros no console
- [ ] Sem erros no servidor

---

**Quando tudo passar, sua plataforma está pronta! 🚀**
