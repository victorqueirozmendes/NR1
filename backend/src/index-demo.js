#!/usr/bin/env node
import express from 'express';
import cors from 'cors';
import { fileURLToPath } from 'url';
import { dirname } from 'path';
import jwt from 'jsonwebtoken';
import bcryptjs from 'bcryptjs';

// Config
const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const app = express();
const PORT = 5000;
const JWT_SECRET = 'sua_chave_secreta_muito_segura_aqui_nr1_2025';

// Middleware
app.use(cors());
app.use(express.json());

// Dados em memória para demonstração
const data = {
  usuarios: [
    {
      id: 1,
      nome: 'Admin NR1',
      email: 'admin@nr1.com',
      senha: '$2a$10$0N8Z.5wpn/vZdM82dXZ6tux.37jvLLtTqlCrP6N/JJ0dNWZ/xYqEy', // 123456
      role: 'admin',
      ativo: true,
      created_at: new Date()
    }
  ],
  cursos: [],
  modulos: [],
  aulas: [],
  materiais: [],
  progresso: [],
  acessos: [],
  nextId: {
    usuarios: 2,
    cursos: 1,
    modulos: 1,
    aulas: 1,
    materiais: 1,
    progresso: 1,
    acessos: 1
  }
};

// Middleware de autenticação
const authMiddleware = (req, res, next) => {
  const token = req.headers.authorization?.replace('Bearer ', '');
  if (!token) return res.status(401).json({ message: 'Token não fornecido' });

  try {
    const decoded = jwt.verify(token, JWT_SECRET);
    req.user = decoded;
    next();
  } catch {
    res.status(401).json({ message: 'Token inválido' });
  }
};

const adminMiddleware = (req, res, next) => {
  if (req.user.role !== 'admin') {
    return res.status(403).json({ message: 'Acesso restrito' });
  }
  next();
};

// ============ AUTH ============
app.post('/api/auth/register', async (req, res) => {
  try {
    const { nome, email, senha } = req.body;

    if (data.usuarios.find(u => u.email === email)) {
      return res.status(400).json({ message: 'Email já cadastrado' });
    }

    const senhaHash = await bcryptjs.hash(senha, 10);
    const usuario = {
      id: data.nextId.usuarios++,
      nome,
      email,
      senha: senhaHash,
      role: 'aluno',
      ativo: true,
      created_at: new Date()
    };

    data.usuarios.push(usuario);
    res.status(201).json({ message: 'Usuário criado com sucesso' });
  } catch (error) {
    res.status(500).json({ message: 'Erro ao registrar' });
  }
});

app.post('/api/auth/login', async (req, res) => {
  try {
    const { email, senha } = req.body;
    const usuario = data.usuarios.find(u => u.email === email && u.ativo);

    if (!usuario) {
      return res.status(401).json({ message: 'Credenciais inválidas' });
    }

    const senhaValida = await bcryptjs.compare(senha, usuario.senha);
    if (!senhaValida) {
      return res.status(401).json({ message: 'Credenciais inválidas' });
    }

    const token = jwt.sign(
      { id: usuario.id, email: usuario.email, role: usuario.role },
      JWT_SECRET,
      { expiresIn: '7d' }
    );

    res.json({
      token,
      usuario: {
        id: usuario.id,
        nome: usuario.nome,
        email: usuario.email,
        role: usuario.role
      }
    });
  } catch (error) {
    res.status(500).json({ message: 'Erro ao fazer login' });
  }
});

app.get('/api/auth/verify', authMiddleware, (req, res) => {
  res.json({ usuario: req.user });
});

// ============ USUÁRIOS ============
app.get('/api/usuarios', authMiddleware, adminMiddleware, (req, res) => {
  res.json(data.usuarios);
});

app.post('/api/usuarios', authMiddleware, adminMiddleware, async (req, res) => {
  try {
    const { nome, email, senha, role } = req.body;

    if (data.usuarios.find(u => u.email === email)) {
      return res.status(400).json({ message: 'Email já existe' });
    }

    const senhaHash = await bcryptjs.hash(senha, 10);
    const usuario = {
      id: data.nextId.usuarios++,
      nome,
      email,
      senha: senhaHash,
      role: role || 'aluno',
      ativo: true,
      created_at: new Date()
    };

    data.usuarios.push(usuario);
    res.status(201).json({ message: 'Usuário criado', id: usuario.id });
  } catch (error) {
    res.status(500).json({ message: 'Erro ao criar usuário' });
  }
});

app.get('/api/usuarios/me', authMiddleware, (req, res) => {
  const usuario = data.usuarios.find(u => u.id === req.user.id);
  if (!usuario) return res.status(404).json({ message: 'Usuário não encontrado' });
  res.json({
    id: usuario.id,
    nome: usuario.nome,
    email: usuario.email,
    role: usuario.role,
    ativo: usuario.ativo
  });
});

app.patch('/api/usuarios/:id/toggle-ativo', authMiddleware, adminMiddleware, (req, res) => {
  const usuario = data.usuarios.find(u => u.id === parseInt(req.params.id));
  if (!usuario) return res.status(404).json({ message: 'Usuário não encontrado' });
  usuario.ativo = req.body.ativo;
  res.json({ message: 'Usuário atualizado' });
});

// ============ CURSOS ============
app.post('/api/cursos', authMiddleware, adminMiddleware, (req, res) => {
  const { nome, descricao } = req.body;
  const curso = {
    id: data.nextId.cursos++,
    nome,
    descricao,
    created_at: new Date()
  };
  data.cursos.push(curso);
  res.status(201).json({ message: 'Curso criado', id: curso.id });
});

app.get('/api/cursos/admin/list', authMiddleware, adminMiddleware, (req, res) => {
  res.json(data.cursos);
});

app.get('/api/cursos/meus', authMiddleware, (req, res) => {
  const acessos = data.acessos.filter(a => a.usuario_id === req.user.id && a.ativo);
  const cursos = data.cursos.filter(c => acessos.some(a => a.curso_id === c.id));
  res.json(cursos);
});

app.patch('/api/cursos/:id', authMiddleware, adminMiddleware, (req, res) => {
  const curso = data.cursos.find(c => c.id === parseInt(req.params.id));
  if (!curso) return res.status(404).json({ message: 'Curso não encontrado' });
  Object.assign(curso, req.body);
  res.json({ message: 'Curso atualizado' });
});

app.post('/api/cursos/liberar-acesso', authMiddleware, adminMiddleware, (req, res) => {
  const { usuarioId, cursoId } = req.body;
  if (data.acessos.some(a => a.usuario_id === usuarioId && a.curso_id === cursoId)) {
    return res.status(400).json({ message: 'Acesso já existe' });
  }
  const acesso = {
    id: data.nextId.acessos++,
    usuario_id: usuarioId,
    curso_id: cursoId,
    ativo: true,
    created_at: new Date()
  };
  data.acessos.push(acesso);
  res.json({ message: 'Acesso liberado' });
});

app.post('/api/cursos/bloquear-acesso', authMiddleware, adminMiddleware, (req, res) => {
  const { usuarioId, cursoId } = req.body;
  const acesso = data.acessos.find(a => a.usuario_id === usuarioId && a.curso_id === cursoId);
  if (acesso) acesso.ativo = false;
  res.json({ message: 'Acesso bloqueado' });
});

// ============ MÓDULOS ============
app.post('/api/modulos', authMiddleware, adminMiddleware, (req, res) => {
  const { cursoId, nome, ordem } = req.body;
  const modulo = {
    id: data.nextId.modulos++,
    curso_id: cursoId,
    nome,
    ordem: ordem || 1,
    created_at: new Date()
  };
  data.modulos.push(modulo);
  res.status(201).json({ message: 'Módulo criado', id: modulo.id });
});

app.get('/api/modulos/curso/:cursoId', authMiddleware, (req, res) => {
  const modulos = data.modulos.filter(m => m.curso_id === parseInt(req.params.cursoId));
  res.json(modulos);
});

app.patch('/api/modulos/:id', authMiddleware, adminMiddleware, (req, res) => {
  const modulo = data.modulos.find(m => m.id === parseInt(req.params.id));
  if (!modulo) return res.status(404).json({ message: 'Módulo não encontrado' });
  Object.assign(modulo, req.body);
  res.json({ message: 'Módulo atualizado' });
});

app.delete('/api/modulos/:id', authMiddleware, adminMiddleware, (req, res) => {
  const index = data.modulos.findIndex(m => m.id === parseInt(req.params.id));
  if (index === -1) return res.status(404).json({ message: 'Módulo não encontrado' });
  data.modulos.splice(index, 1);
  res.json({ message: 'Módulo deletado' });
});

// ============ AULAS ============
app.post('/api/aulas', authMiddleware, adminMiddleware, (req, res) => {
  const { moduloId, titulo, descricao, videoUrl, ordem } = req.body;
  const aula = {
    id: data.nextId.aulas++,
    modulo_id: moduloId,
    titulo,
    descricao,
    video_url: videoUrl,
    ordem: ordem || 1,
    created_at: new Date()
  };
  data.aulas.push(aula);
  res.status(201).json({ message: 'Aula criada', id: aula.id });
});

app.get('/api/aulas/modulo/:moduloId', authMiddleware, (req, res) => {
  const aulas = data.aulas.filter(a => a.modulo_id === parseInt(req.params.moduloId));
  res.json(aulas);
});

app.get('/api/aulas/:id', authMiddleware, (req, res) => {
  const aula = data.aulas.find(a => a.id === parseInt(req.params.id));
  if (!aula) return res.status(404).json({ message: 'Aula não encontrada' });
  res.json(aula);
});

app.patch('/api/aulas/:id', authMiddleware, adminMiddleware, (req, res) => {
  const aula = data.aulas.find(a => a.id === parseInt(req.params.id));
  if (!aula) return res.status(404).json({ message: 'Aula não encontrada' });
  Object.assign(aula, req.body);
  res.json({ message: 'Aula atualizada' });
});

app.delete('/api/aulas/:id', authMiddleware, adminMiddleware, (req, res) => {
  const index = data.aulas.findIndex(a => a.id === parseInt(req.params.id));
  if (index === -1) return res.status(404).json({ message: 'Aula não encontrada' });
  data.aulas.splice(index, 1);
  res.json({ message: 'Aula deletada' });
});

// ============ MATERIAIS ============
app.post('/api/materiais', authMiddleware, adminMiddleware, (req, res) => {
  const { aulaId, tipo, url, nome } = req.body;
  const material = {
    id: data.nextId.materiais++,
    aula_id: aulaId,
    tipo,
    url,
    nome,
    created_at: new Date()
  };
  data.materiais.push(material);
  res.status(201).json({ message: 'Material adicionado', id: material.id });
});

app.get('/api/materiais/aula/:aulaId', authMiddleware, (req, res) => {
  const materiais = data.materiais.filter(m => m.aula_id === parseInt(req.params.aulaId));
  res.json(materiais);
});

app.delete('/api/materiais/:id', authMiddleware, adminMiddleware, (req, res) => {
  const index = data.materiais.findIndex(m => m.id === parseInt(req.params.id));
  if (index === -1) return res.status(404).json({ message: 'Material não encontrado' });
  data.materiais.splice(index, 1);
  res.json({ message: 'Material deletado' });
});

// ============ PROGRESSO ============
app.post('/api/progresso/marcar-concluida', authMiddleware, (req, res) => {
  const { aulaId } = req.body;
  const prog = data.progresso.find(p => p.usuario_id === req.user.id && p.aula_id === aulaId);
  if (prog) {
    prog.concluida = true;
  } else {
    data.progresso.push({
      id: data.nextId.progresso++,
      usuario_id: req.user.id,
      aula_id: aulaId,
      concluida: true,
      created_at: new Date()
    });
  }
  res.json({ message: 'Aula marcada como concluída' });
});

app.get('/api/progresso/curso/:cursoId', authMiddleware, (req, res) => {
  const modulos = data.modulos.filter(m => m.curso_id === parseInt(req.params.cursoId));
  const aulasIds = data.aulas.filter(a => modulos.some(m => m.id === a.modulo_id)).map(a => a.id);
  const total = aulasIds.length;
  const concluidas = data.progresso.filter(p => p.usuario_id === req.user.id && aulasIds.includes(p.aula_id) && p.concluida).length;
  res.json({
    total,
    concluidas,
    percentual: total > 0 ? Math.round((concluidas / total) * 100) : 0
  });
});

app.get('/api/progresso/aluno/:usuarioId/curso/:cursoId', authMiddleware, adminMiddleware, (req, res) => {
  const modulos = data.modulos.filter(m => m.curso_id === parseInt(req.params.cursoId));
  const aulasIds = data.aulas.filter(a => modulos.some(m => m.id === a.modulo_id)).map(a => a.id);
  const total = aulasIds.length;
  const concluidas = data.progresso.filter(p => p.usuario_id === parseInt(req.params.usuarioId) && aulasIds.includes(p.aula_id) && p.concluida).length;
  res.json({
    total,
    concluidas,
    percentual: total > 0 ? Math.round((concluidas / total) * 100) : 0
  });
});

// Health check
app.get('/health', (req, res) => {
  res.json({ status: 'ok', mode: 'demo-in-memory' });
});

// Iniciar servidor
app.listen(PORT, () => {
  console.log(`
╔════════════════════════════════════════════════════════════╗
║  🚀 NR1 EAD - Backend Rodando (Modo Demonstração)        ║
╚════════════════════════════════════════════════════════════╝

✅ Servidor: http://localhost:${PORT}
✅ Health: http://localhost:${PORT}/health
✅ Modo: Demonstração em memória (dados não persistem)

📚 Primeiro Login:
   Email: admin@nr1.com
   Senha: 123456

⚠️  Nota: Este é modo demo. Para produção use MySQL real.

Pressione Ctrl+C para parar o servidor.
  `);
});
