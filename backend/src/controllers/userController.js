import { query, queryOne } from '../config/database.js';
import bcrypt from 'bcryptjs';

export async function getUsers(req, res) {
  try {
    const users = await query('SELECT id, nome, email, role, ativo FROM users');
    res.json(users);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao buscar usuários' });
  }
}

export async function createUser(req, res) {
  try {
    const { nome, email, senha, role } = req.body;

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
      [nome, email, senhaHash, role || 'aluno', true]
    );

    const user = await queryOne('SELECT id, nome, email, role FROM users WHERE id = ?', [result.insertId]);
    res.status(201).json(user);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao criar usuário' });
  }
}

export async function updateUser(req, res) {
  try {
    const { id } = req.params;
    const { nome, email, role, ativo } = req.body;

    const user = await queryOne('SELECT * FROM users WHERE id = ?', [id]);
    if (!user) {
      return res.status(404).json({ message: 'Usuário não encontrado' });
    }

    await query(
      'UPDATE users SET nome = ?, email = ?, role = ?, ativo = ? WHERE id = ?',
      [nome || user.nome, email || user.email, role ?? user.role, ativo ?? user.ativo, id]
    );

    const updatedUser = await queryOne('SELECT id, nome, email, role, ativo FROM users WHERE id = ?', [id]);
    res.json(updatedUser);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao atualizar usuário' });
  }
}

export async function deleteUser(req, res) {
  try {
    const { id } = req.params;

    const user = await queryOne('SELECT * FROM users WHERE id = ?', [id]);
    if (!user) {
      return res.status(404).json({ message: 'Usuário não encontrado' });
    }

    await query('DELETE FROM users WHERE id = ?', [id]);
    res.json({ message: 'Usuário deletado com sucesso' });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao deletar usuário' });
  }
}

export async function deactivateUser(req, res) {
  try {
    const { id } = req.params;

    const user = await queryOne('SELECT * FROM users WHERE id = ?', [id]);
    if (!user) {
      return res.status(404).json({ message: 'Usuário não encontrado' });
    }

    await query('UPDATE users SET ativo = ? WHERE id = ?', [false, id]);
    res.json({ message: 'Usuário desativado com sucesso' });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao desativar usuário' });
  }
}
