# 💡 Exemplos de Uso da API

## Setup Inicial

```bash
# Url base
BASE_URL=http://localhost:5000/api
```

## 1️⃣ Autenticação

### Registrar novo aluno
```bash
curl -X POST http://localhost:5000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "nome": "João Silva",
    "email": "joao@example.com",
    "senha": "senha123"
  }'

# Response
{
  "message": "Usuário registrado com sucesso",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": 1,
    "nome": "João Silva",
    "email": "joao@example.com",
    "role": "aluno"
  }
}
```

### Login
```bash
TOKEN=$(curl -X POST http://localhost:5000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@nr1.com",
    "senha": "Admin@123456"
  }' | jq -r '.token')

echo $TOKEN
```

### Obter dados do usuário
```bash
curl -X GET http://localhost:5000/api/auth/me \
  -H "Authorization: Bearer $TOKEN"
```

## 2️⃣ Gerenciar Cursos (Admin)

### Criar novo curso
```bash
curl -X POST http://localhost:5000/api/courses \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "nome": "React Avançado",
    "descricao": "Aprenda React do zero ao profissional"
  }'
```

### Listar cursos
```bash
curl -X GET http://localhost:5000/api/courses
```

### Liberar acesso a um curso para aluno
```bash
curl -X POST http://localhost:5000/api/courses/grant-access \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "userId": 2,
    "courseId": 1
  }'
```

## 3️⃣ Gerenciar Módulos e Aulas (Admin)

### Criar módulo em um curso
```bash
curl -X POST http://localhost:5000/api/lessons/modules \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "courseId": 1,
    "nome": "Fundamentos",
    "ordem": 1
  }'
```

### Criar aula em um módulo
```bash
curl -X POST http://localhost:5000/api/lessons \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "moduleId": 1,
    "titulo": "Introdução ao React",
    "descricao": "Nesta aula vamos aprender o básico",
    "video_url": "https://www.youtube.com/embed/dQw4w9WgXcQ",
    "ordem": 1
  }'
```

### Adicionar material (PDF, link, arquivo)
```bash
curl -X POST http://localhost:5000/api/lessons/materials \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "lessonId": 1,
    "tipo": "pdf",
    "titulo": "Slides da Aula",
    "url": "https://s3.amazonaws.com/meu-bucket/slides.pdf"
  }'
```

## 4️⃣ Rastrear Progresso (Aluno)

### Marcar aula como concluída
```bash
curl -X POST http://localhost:5000/api/progress/mark-complete \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "lessonId": 1
  }'
```

### Ver progresso pessoal
```bash
curl -X GET http://localhost:5000/api/progress \
  -H "Authorization: Bearer $TOKEN"
```

### Ver progresso em um curso específico
```bash
curl -X GET http://localhost:5000/api/progress/course/1 \
  -H "Authorization: Bearer $TOKEN"

# Response
{
  "courseId": 1,
  "totalLessons": 5,
  "completedLessons": 2,
  "percentage": 40
}
```

## 5️⃣ Gerenciar Usuários (Admin)

### Listar todos os usuários
```bash
curl -X GET http://localhost:5000/api/users \
  -H "Authorization: Bearer $TOKEN"
```

### Criar novo usuário manualmente
```bash
curl -X POST http://localhost:5000/api/users \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "nome": "Maria Santos",
    "email": "maria@example.com",
    "senha": "senha123",
    "role": "aluno"
  }'
```

### Atualizar usuário
```bash
curl -X PUT http://localhost:5000/api/users/2 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "nome": "Maria Silva",
    "role": "aluno",
    "ativo": true
  }'
```

### Desativar usuário
```bash
curl -X PATCH http://localhost:5000/api/users/2/deactivate \
  -H "Authorization: Bearer $TOKEN"
```

### Deletar usuário
```bash
curl -X DELETE http://localhost:5000/api/users/2 \
  -H "Authorization: Bearer $TOKEN"
```

## 🔍 Verificar Status do Servidor

```bash
curl -X GET http://localhost:5000/health

# Response
{
  "status": "OK",
  "timestamp": "2024-12-23T10:30:00.000Z"
}
```

## 📱 Exemplos com JavaScript/Fetch

### Login e salvar token
```javascript
async function login() {
  const response = await fetch('http://localhost:5000/api/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      email: 'admin@nr1.com',
      senha: 'Admin@123456'
    })
  });
  
  const data = await response.json();
  localStorage.setItem('token', data.token);
  return data;
}
```

### Fazer requisição autenticada
```javascript
async function getMyCourses() {
  const token = localStorage.getItem('token');
  const response = await fetch('http://localhost:5000/api/courses/my-courses', {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  
  return await response.json();
}
```

### Criar novo curso
```javascript
async function createCourse(nome, descricao) {
  const token = localStorage.getItem('token');
  const response = await fetch('http://localhost:5000/api/courses', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify({ nome, descricao })
  });
  
  return await response.json();
}
```

## 🎬 Fluxo Completo de Exemplo

### 1. Admin cria um curso
```bash
COURSE_RESPONSE=$(curl -s -X POST http://localhost:5000/api/courses \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"nome": "Node.js", "descricao": "Aprenda Node.js"}')

COURSE_ID=$(echo $COURSE_RESPONSE | jq -r '.id')
echo "Curso criado: $COURSE_ID"
```

### 2. Admin cria módulo
```bash
MODULE_RESPONSE=$(curl -s -X POST http://localhost:5000/api/lessons/modules \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d "{\"courseId\": $COURSE_ID, \"nome\": \"Módulo 1\"}")

MODULE_ID=$(echo $MODULE_RESPONSE | jq -r '.id')
echo "Módulo criado: $MODULE_ID"
```

### 3. Admin cria aula
```bash
LESSON_RESPONSE=$(curl -s -X POST http://localhost:5000/api/lessons \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d "{\"moduleId\": $MODULE_ID, \"titulo\": \"Aula 1\"}")

LESSON_ID=$(echo $LESSON_RESPONSE | jq -r '.id')
echo "Aula criada: $LESSON_ID"
```

### 4. Admin libera acesso para aluno
```bash
curl -s -X POST http://localhost:5000/api/courses/grant-access \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d "{\"userId\": 2, \"courseId\": $COURSE_ID}"
```

### 5. Aluno vê seus cursos (com seu token)
```bash
STUDENT_TOKEN="..."  # Token do aluno
curl -s -X GET http://localhost:5000/api/courses/my-courses \
  -H "Authorization: Bearer $STUDENT_TOKEN"
```

### 6. Aluno marca aula como concluída
```bash
curl -s -X POST http://localhost:5000/api/progress/mark-complete \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $STUDENT_TOKEN" \
  -d "{\"lessonId\": $LESSON_ID}"
```

### 7. Admin verifica progresso do aluno
```bash
curl -s -X GET http://localhost:5000/api/progress/student/2 \
  -H "Authorization: Bearer $TOKEN"
```

---

**Dica**: Use ferramentas como [Postman](https://www.postman.com) ou [Insomnia](https://insomnia.rest) para testar a API com interface gráfica!
