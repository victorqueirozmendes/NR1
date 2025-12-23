# API Documentation - NR1 EAD Platform

## Base URL
```
http://localhost:5000/api
```

## Authentication
All protected routes require a JWT token in the Authorization header:
```
Authorization: Bearer <token>
```

## Endpoints

### Auth
#### Register
```
POST /auth/register
Body: {
  "nome": "string",
  "email": "string",
  "senha": "string"
}
Response: {
  "message": "string",
  "token": "string",
  "user": { id, nome, email, role }
}
```

#### Login
```
POST /auth/login
Body: {
  "email": "string",
  "senha": "string"
}
Response: {
  "message": "string",
  "token": "string",
  "user": { id, nome, email, role }
}
```

#### Get Current User
```
GET /auth/me
Headers: Authorization: Bearer <token>
Response: { id, nome, email, role }
```

### Courses (Public)
```
GET /courses
Response: [
  { id, nome, descricao, criado_em }
]

GET /courses/:id
Response: { id, nome, descricao, criado_em }
```

### Courses (Student)
```
GET /courses/my-courses
Headers: Authorization: Bearer <token>
Response: [
  { id, nome, descricao, criado_em }
]
```

### Courses (Admin)
```
POST /courses
Headers: Authorization: Bearer <token>
Body: { "nome": "string", "descricao": "string" }
Response: { id, nome, descricao, criado_em }

PUT /courses/:id
Headers: Authorization: Bearer <token>
Body: { "nome": "string", "descricao": "string" }
Response: { id, nome, descricao, criado_em }

DELETE /courses/:id
Headers: Authorization: Bearer <token>
Response: { message: "Curso deletado com sucesso" }

POST /courses/grant-access
Headers: Authorization: Bearer <token>
Body: { "userId": number, "courseId": number }
Response: { message: "Acesso liberado com sucesso" }

POST /courses/revoke-access
Headers: Authorization: Bearer <token>
Body: { "userId": number, "courseId": number }
Response: { message: "Acesso revogado com sucesso" }
```

### Lessons
```
GET /lessons?moduleId=<id>
Response: [
  { id, module_id, titulo, descricao, video_url, ordem }
]

GET /lessons/:id
Response: {
  id, module_id, titulo, descricao, video_url, ordem,
  materials: [
    { id, lesson_id, tipo, titulo, url }
  ]
}

POST /lessons (Admin)
Headers: Authorization: Bearer <token>
Body: {
  "moduleId": number,
  "titulo": "string",
  "descricao": "string",
  "video_url": "string",
  "ordem": number
}
Response: { id, module_id, titulo, descricao, video_url, ordem }

PUT /lessons/:id (Admin)
Headers: Authorization: Bearer <token>
Body: { "titulo", "descricao", "video_url", "ordem" }
Response: { id, module_id, titulo, descricao, video_url, ordem }

DELETE /lessons/:id (Admin)
Headers: Authorization: Bearer <token>
Response: { message: "Aula deletada com sucesso" }
```

### Modules (Admin)
```
GET /lessons/modules/list?courseId=<id>
Response: [
  {
    id, course_id, nome, ordem,
    lessons: [ { id, titulo, video_url, ordem } ]
  }
]

POST /lessons/modules
Headers: Authorization: Bearer <token>
Body: { "courseId": number, "nome": "string", "ordem": number }
Response: { id, course_id, nome, ordem }
```

### Materials (Admin)
```
POST /lessons/materials
Headers: Authorization: Bearer <token>
Body: {
  "lessonId": number,
  "tipo": "pdf|link|arquivo",
  "titulo": "string",
  "url": "string"
}
Response: { id, lesson_id, tipo, titulo, url }
```

### Progress
```
GET /progress
Headers: Authorization: Bearer <token>
Response: [
  { id, user_id, lesson_id, concluida, criado_em, atualizado_em }
]

GET /progress/course/:courseId
Headers: Authorization: Bearer <token>
Response: {
  courseId: number,
  totalLessons: number,
  completedLessons: number,
  percentage: number
}

POST /progress/mark-complete
Headers: Authorization: Bearer <token>
Body: { "lessonId": number }
Response: { message: "Aula marcada como concluída" }
```

### Progress (Admin)
```
GET /progress/student/:studentId
Headers: Authorization: Bearer <token>
Response: [
  {
    id, user_id, lesson_id, concluida,
    lesson_title, module_name, course_name
  }
]
```

### Users (Admin)
```
GET /users
Headers: Authorization: Bearer <token>
Response: [
  { id, nome, email, role, ativo }
]

POST /users
Headers: Authorization: Bearer <token>
Body: {
  "nome": "string",
  "email": "string",
  "senha": "string",
  "role": "admin|aluno"
}
Response: { id, nome, email, role }

PUT /users/:id
Headers: Authorization: Bearer <token>
Body: { "nome", "email", "role", "ativo" }
Response: { id, nome, email, role, ativo }

DELETE /users/:id
Headers: Authorization: Bearer <token>
Response: { message: "Usuário deletado com sucesso" }

PATCH /users/:id/deactivate
Headers: Authorization: Bearer <token>
Response: { message: "Usuário desativado com sucesso" }
```

## Error Responses

### 400 Bad Request
```json
{ "message": "Campo obrigatório faltando" }
```

### 401 Unauthorized
```json
{ "message": "Token não fornecido" }
```

### 403 Forbidden
```json
{ "message": "Acesso negado. Apenas administradores." }
```

### 404 Not Found
```json
{ "message": "Recurso não encontrado" }
```

### 500 Internal Server Error
```json
{ "message": "Erro interno do servidor" }
```
