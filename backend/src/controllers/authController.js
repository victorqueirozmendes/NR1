import { query, queryOne } from '../config/database.js';
import bcrypt from 'bcryptjs';
import { generateToken } from '../config/jwt.js';

export async function register(req, res) {
  try {
    const { nome, email, senha } = req.body;

    if (!nome || !email || !senha) {
      return res.status(400).json({ message: 'Nome, email e senha são obrigatórios' });
    }

    const existingUser = await queryOne('SELECT id FROM users WHERE email = ?', [email]);
    if (existingUser) {
      return res.status(400).json({ message: 'Email já registrado' });
    }

    const senhaHash = await bcrypt.hash(senha, 10);

    const result = await query(
      'INSERT INTO users (nome, email, senha, role, ativo) VALUES (?, ?, ?, ?, ?)',
      [nome, email, senhaHash, 'aluno', true]
    );

    const user = await queryOne('SELECT id, nome, email, role FROM users WHERE id = ?', [result.insertId]);
    const token = generateToken(user);

    res.status(201).json({
      message: 'Usuário registrado com sucesso',
      token,
      user
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao registrar usuário' });
  }
}

export async function login(req, res) {
  try {
    const { email, senha } = req.body;

    if (!email || !senha) {
      return res.status(400).json({ message: 'Email e senha são obrigatórios' });
    }

    const user = await queryOne('SELECT * FROM users WHERE email = ?', [email]);
    if (!user) {
      return res.status(401).json({ message: 'Email ou senha incorretos' });
    }

    if (!user.ativo) {
      return res.status(403).json({ message: 'Usuário desativado' });
    }

    const senhaValida = await bcrypt.compare(senha, user.senha);
    if (!senhaValida) {
      return res.status(401).json({ message: 'Email ou senha incorretos' });
    }

    const token = generateToken(user);

    res.json({
      message: 'Login realizado com sucesso',
      token,
      user: {
        id: user.id,
        nome: user.nome,
        email: user.email,
        role: user.role
      }
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao fazer login' });
  }
}

export async function me(req, res) {
  try {
    const user = await queryOne('SELECT id, nome, email, role FROM users WHERE id = ?', [req.user.id]);
    res.json(user);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao buscar usuário' });
  }
}
