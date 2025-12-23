@echo off
REM Script de inicialização da plataforma NR1 EAD para Windows
REM Uso: init.bat

echo.
echo 🚀 Inicializando NR1 EAD Platform...
echo.

REM Verificar Node.js
echo Verificando Node.js...
where node >nul 2>nul
if %errorlevel% neq 0 (
  echo ❌ Node.js não encontrado. Por favor, instale Node.js 16+
  exit /b 1
)
for /f "tokens=*" %%i in ('node -v') do set NODE_VERSION=%%i
echo ✅ Node.js %NODE_VERSION% encontrado
echo.

REM Setup Backend
echo Configurando Backend...
cd backend

if not exist .env (
  echo ℹ️  Criando .env do backend...
  copy .env.example .env
  echo ✅ .env criado (edite com suas credenciais MySQL)
)

if not exist node_modules (
  echo ℹ️  Instalando dependências do backend...
  call npm install
  echo ✅ Dependências instaladas
) else (
  echo ✅ Dependências do backend já instaladas
)

cd ..
echo.

REM Setup Frontend
echo Configurando Frontend...
cd frontend

if not exist node_modules (
  echo ℹ️  Instalando dependências do frontend...
  call npm install
  echo ✅ Dependências instaladas
) else (
  echo ✅ Dependências do frontend já instaladas
)

cd ..
echo.

REM Resumo
echo ✅ Setup inicial concluído!
echo.
echo ℹ️  Próximos passos:
echo   1. Edite o arquivo: backend\.env com suas credenciais MySQL
echo   2. Execute as migrações: cd backend ^&^& npm run migrate
echo   3. Inicie o backend: cd backend ^&^& npm run dev
echo   4. Inicie o frontend: cd frontend ^&^& npm run dev
echo.
echo ℹ️  URLs:
echo   Frontend: http://localhost:5173
echo   Backend: http://localhost:5000
echo   API: http://localhost:5000/api
echo.
echo ℹ️  Credenciais padrão:
echo   Email: admin@nr1.com
echo   Senha: Admin@123456
echo.
