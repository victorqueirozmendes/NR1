# ❓ FAQ - NR1 EAD

## Instalação & Setup

### P: Como instalar a plataforma?
**R**: Siga os passos em `DOCUMENTACAO.md`:
1. Clone o repositório
2. Crie o banco com `database-init.sql`
3. Aplique atualizações: `database-updates.sql`
4. Configure `/includes/db.php`
5. Crie `/uploads/materiais/`

### P: Qual versão de PHP é necessária?
**R**: PHP 7.4 ou superior. Compatível com PHP 8.0+.

### P: Como alterar credenciais de admin?
**R**: No banco de dados:
```sql
UPDATE usuarios SET 
  email='novo@email.com', 
  senha=PASSWORD(SHA2('sua_senha', 256)) 
WHERE role='admin' LIMIT 1;
```

### P: Posso usar em produção?
**R**: Sim! Mas recomenda-se:
- Ativar HTTPS
- Desativar erros do PHP (`display_errors = Off`)
- Fazer backup regular do banco
- Usar certificado SSL válido

---

## YouTube

### P: Qual é o formato da URL do YouTube?
**R**: Qualquer formato funcionará:
- `https://www.youtube.com/watch?v=ID_AQUI`
- `https://youtu.be/ID_AQUI`
- `https://www.youtube.com/embed/ID_AQUI`

O sistema extrai automaticamente o ID.

### P: Posso usar playlists do YouTube?
**R**: Não, apenas vídeos individuais. Para playlist:
1. Copie o ID do primeiro vídeo
2. Use em uma aula separada
3. Repita para cada vídeo

### P: O vídeo funciona em mobile?
**R**: Sim! O iframe é 100% responsivo com aspect ratio 16:9.

### P: Posso alterar tamanho do vídeo?
**R**: Edite em `css/style-mobile-first.css`:
```css
.video-container {
    padding-bottom: 56.25%; /* Mude este valor */
}
```

### P: Aluno pode baixar o vídeo?
**R**: Não. O YouTube não permite. Está bloqueado por padrão.

---

## Controle de Acesso

### P: Qual a diferença entre "Completo", "Limitado" e "Bloqueado"?
**R**:
- **Completo**: Acesso total a tudo (aulas + materiais)
- **Limitado**: Pode ver aulas, mas materiais bloqueados (depende do checkbox)
- **Bloqueado**: Não vê o curso

### P: Como dar acesso a todos os alunos de uma vez?
**R**: Atualmente precisa fazer um a um. Para futuro, peça feature de "bulk import".

### P: Posso criar níveis customizados?
**R**: Não por UI. Mas você pode modificar no banco:
```sql
ALTER TABLE user_access_control 
MODIFY access_level ENUM('nivel1', 'nivel2', 'nivel3');
```

### P: Aluno pode ver que foi bloqueado?
**R**: Não, o curso simplesmente não aparece na lista.

### P: Como resetar permissões de um aluno?
**R**: Delete a linha na tabela:
```sql
DELETE FROM user_access_control 
WHERE usuario_id = 5 AND curso_id = 2;
```

---

## Materiais (PDF)

### P: Qual é o tamanho máximo do arquivo?
**R**: 50MB por arquivo.

### P: Posso mudar o limite?
**R**: Sim, em `admin/material-upload.php`, linha ~35:
```php
} elseif ($arquivo['size'] > 100 * 1024 * 1024) { // 100MB
```

### P: Que tipos de arquivo são aceitos?
**R**: Apenas PDF. Para aceitar outros, modifique a validação.

### P: Onde os arquivos são armazenados?
**R**: Em `/uploads/materiais/` com nome aleatório.

### P: Posso ver lista de todos os materiais?
**R**: Sim, em Admin → Materiais.

---

## Autenticação

### P: Como reset senha de um aluno?
**R**: No banco de dados (admin precisa fazer):
```sql
UPDATE usuarios SET senha='$2y$10$...' 
WHERE id=5;
```

Ou melhor: criar feature de "esqueçi minha senha"

### P: Quanto tempo a sessão fica ativa?
**R**: Por padrão, até fechar navegador. Para sessão persistente, edite `includes/auth.php`.

### P: Posso usar login com Google/Facebook?
**R**: Não está implementado. Seria necessário OAuth.

---

## Progresso & Comentários

### P: Como ver o progresso de todos os alunos?
**R**: Consulte tabela `progresso` no banco:
```sql
SELECT u.nome, a.titulo, p.completado, p.data_conclusao
FROM progresso p
JOIN usuarios u ON p.usuario_id = u.id
JOIN aulas a ON p.aula_id = a.id
WHERE p.completado = 1
ORDER BY u.nome;
```

### P: Posso exportar relatório de progresso?
**R**: Não está integrado. Use MySQL Workbench ou seu próprio script.

### P: Como ocultar comentários inadequados?
**R**: No banco:
```sql
UPDATE comentarios SET oculto = 1 WHERE id = 5;
```

Ou implemente UI para admin.

---

## Performance

### P: O site está lento, como melhorar?
**R**:
1. Adicione índices no banco (estão em `database-updates.sql`)
2. Use CDN para CSS
3. Comprima imagens
4. Ative gzip no servidor
5. Use cache HTTP

### P: Quantos alunos podem usar simultaneamente?
**R**: Depende do servidor. Com especificação média:
- ~500 alunos concurrent
- Máximo 5000 alunos registrados

### P: Como aumentar limite de upload?
**R**: Edite `php.ini`:
```ini
upload_max_filesize = 100M
post_max_size = 100M
```

---

## Segurança

### P: A senha é segura?
**R**: Sim, usa bcrypt com salt. Extremamente segura.

### P: Posso ver a senha dos alunos?
**R**: Não, e é bom assim! Nem admin pode ver.

### P: Está protegido contra SQL Injection?
**R**: Sim, usa prepared statements em tudo.

### P: Como ativar HTTPS?
**R**: No servidor:
1. Obtenha certificado SSL (Let's Encrypt)
2. Configure no Apache/Nginx
3. Redirecione HTTP → HTTPS

---

## Design & Layout

### P: Como alterar as cores?
**R**: Edite `css/style-mobile-first.css`, linhas 10-18:
```css
:root {
    --primary: #3498db;      /* Azul */
    --secondary: #2c3e50;    /* Preto */
    /* ... outros ... */
}
```

### P: Como adicionar logo?
**R**: Edite `navbar-brand` no CSS e altere `/` para `/admin/` com logo.

### P: Mobile não funciona bem?
**R**: Verifique:
1. Viewport meta tag existe? ✅
2. CSS breakpoints? ✅ (768px, 1024px)
3. Imagens responsivas? ✅

### P: Como adicionar dark mode?
**R**: Seria necessário reescrever CSS. Deixe como sugestão futura.

---

## Banco de Dados

### P: Posso usar PostgreSQL em vez de MySQL?
**R**: Seria necessário reescrever queries. Atualmente é MySQL only.

### P: Como fazer backup?
**R**:
```bash
mysqldump -u root -p NR1 > backup.sql
# Restaurar:
mysql -u root -p NR1 < backup.sql
```

### P: Como aumentar limite de dados?
**R**: Altere tipos no banco:
```sql
ALTER TABLE aulas MODIFY conteudo LONGBLOB; /* Para suportar mais dados */
```

### P: Posso usar tabelão em vez de múltiplas tabelas?
**R**: Tecnicamente sim, mas não é recomendado. Normalização é importante.

---

## Suporte & Desenvolvimento

### P: Como reportar bug?
**R**: Abra issue no GitHub com:
1. Descrição do problema
2. Passos para reproduzir
3. Versão do PHP/MySQL
4. Screenshot (se aplicável)

### P: Como sugerir feature?
**R**: Abra uma discussion no GitHub ou entre em contato.

### P: Posso modificar o código?
**R**: Sim! Está sob MIT License.

### P: Como contribuir?
**R**:
1. Fork do repositório
2. Crie uma branch (`git checkout -b feature/MinhaFeature`)
3. Faça commits (`git commit -m 'Add MinhaFeature'`)
4. Push para a branch (`git push origin feature/MinhaFeature`)
5. Abra um Pull Request

---

## Troubleshooting Comum

### P: "Access Denied" ao conectar no banco?
**R**: Verifique em `/includes/db.php`:
```php
$username = "seu_usuario_aqui";
$password = "sua_senha_aqui";
$database = "NR1";
```

### P: "File not found" ao tentar upload?
**R**:
```bash
mkdir -p /uploads/materiais
chmod 777 /uploads/materiais
```

### P: Vídeo YouTube não aparece?
**R**:
1. Verifique se URL é válida
2. Certifique de que correr `database-updates.sql`
3. Limpe cache do navegador

### P: Controle de acesso não funciona?
**R**:
1. Tabela `user_access_control` existe?
2. Funções em `auth.php` foram adicionadas?
3. Usuário tem acesso ao curso?

---

## Roadmap (Futuras Melhorias)

- [ ] Dash board com gráficos de progresso
- [ ] Email de notificações
- [ ] Certificados automáticos
- [ ] Integração com Stripe (vendas)
- [ ] Mobile app (iOS/Android)
- [ ] Live chat entre alunos
- [ ] Quizzes e testes
- [ ] Sistema de pontos/badges
- [ ] Relatórios avançados
- [ ] Dark mode

---

## Contato & Suporte

📧 Email: seu-email@exemplo.com
💬 Discord: seu-link-discord
🐙 GitHub: seu-repositorio
📱 WhatsApp: +55 11 XXXXX-XXXX

---

**Obrigado por usar NR1 EAD! 🎓**

Para mais informações, consulte `DOCUMENTACAO.md` ou `RESUMO_MUDANCAS.md`
