# 🎓 NR1 - Plataforma EAD

## ⚡ Comece em 5 minutos!

```bash
# 1. Setup automático
bash init.sh            # ou init.bat no Windows

# 2. Configure o banco
cd backend
nano .env              # edite com suas credenciais MySQL

# 3. Crie as tabelas
npm run migrate

# 4. Inicie os servidores
npm run dev            # em um terminal
cd ../frontend && npm run dev    # em outro terminal

# 5. Acesse
# Frontend: http://localhost:5173
# Admin: admin@nr1.com / Admin@123456
```

---

## 📚 Documentação

| Documento | Propósito |
|-----------|-----------|
| **QUICKSTART.md** | Início rápido |
| **SETUP.md** | Guia detalhado |
| **README.md** | Visão geral completa |
| **ESTRUTURA.txt** | Mapa do projeto |
| **RESUMO.md** | O que foi criado |
| **docs/API.md** | Endpoints da API |
| **docs/EXEMPLOS.md** | Exemplos de uso |

---

## 🚀 Próximo Passo

👉 **Leia o arquivo QUICKSTART.md**

Tem tudo o que você precisa para começar!

---

## 💡 Dicas Rápidas

- MySQL precisa estar rodando
- Node.js 16+ necessário
- Use dois terminais (um para backend, outro para frontend)
- Token JWT é salvo automaticamente no localStorage

---

**Desenvolvido com ❤️ para educação de qualidade**
