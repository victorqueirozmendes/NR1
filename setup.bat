@echo off
echo 🚀 NR1 EAD - Inicialização Automática
echo ====================================

echo.
echo 1️⃣  Instalando dependências do Backend...
cd backend
call npm install
echo ✅ Backend instalado
echo.

echo 2️⃣  Copiando arquivo .env do Backend...
if not exist .env (
    copy .env.example .env
    echo ⚠️  Configure o arquivo backend\.env com suas credenciais MySQL
) else (
    echo ✅ .env já existe
)
echo.

echo 3️⃣  Instalando dependências do Frontend...
cd ..\frontend
call npm install
echo ✅ Frontend instalado
echo.

echo 4️⃣  Copiando arquivo .env do Frontend...
if not exist .env (
    copy .env.example .env
    echo ✅ .env criado
) else (
    echo ✅ .env já existe
)
echo.

echo ===================================
echo ✅ Setup concluído com sucesso!
echo ===================================
echo.

echo 📋 Próximos passos:
echo 1. Configure o arquivo backend\.env com suas credenciais MySQL
echo 2. Execute: npm run migrate (dentro de backend\)
echo 3. Abra 2 terminais:
echo    - Terminal 1: cd backend ^&^& npm run dev
echo    - Terminal 2: cd frontend ^&^& npm run dev
echo 4. Acesse http://localhost:3000
echo.

echo 📚 Documentação:
echo - QUICKSTART.md   - Início rápido
echo - SETUP.md        - Guia detalhado
echo - README.md       - Visão geral completa
echo - docs\API.md     - Documentação da API
echo - docs\EXEMPLOS.md - Exemplos de uso
echo.

echo 💡 Dica: Se tiver problemas, verifique o arquivo TROUBLESHOOTING.md
echo.
pause
