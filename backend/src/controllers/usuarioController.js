const pool = require('../config/database');
const { hashPassword } = require('../config/auth');

class UsuarioController {
  // Admin: Criar usuário manualmente
  static async createUsuario(req, res) {
    try {
      const { nome, email, senha, role } = req.body;

      const connection = await pool.getConnection();

      const [existing] = await connection.execute(
        'SELECT id FROM usuarios WHERE email = ?',
        [email]
      );

      if (existing.length > 0) {
        connection.release();
        return res.status(400).json({ message: 'Email já existe' });
      }

      const senhaHash = await hashPassword(senha);

      const [result] = await connection.execute(
        'INSERT INTO usuarios (nome, email, senha, role, ativo) VALUES (?, ?, ?, ?, ?)',
        [nome, email, senhaHash, role || 'aluno', true]
      );

      connection.release();

      res.status(201).json({
        message: 'Usuário criado',
        id: result.insertId,
      });
    } catch (error) {
      console.error('Erro ao criar usuário:', error);
      res.status(500).json({ message: 'Erro ao criar usuário' });
    }
  }

  // Admin: Listar todos os usuários
  static async listarUsuarios(req, res) {
    try {
      const connection = await pool.getConnection();

      const [usuarios] = await connection.execute(
        'SELECT id, nome, email, role, ativo, created_at FROM usuarios ORDER BY created_at DESC'
      );

      connection.release();

      res.json(usuarios);
    } catch (error) {
      console.error('Erro ao listar usuários:', error);
      res.status(500).json({ message: 'Erro ao listar usuários' });
    }
  }

  // Admin: Bloquear/Ativar usuário
  static async toggleAtivoUsuario(req, res) {
    try {
      const { id } = req.params;
      const { ativo } = req.body;

      const connection = await pool.getConnection();

      await connection.execute('UPDATE usuarios SET ativo = ? WHERE id = ?', [
        ativo,
        id,
      ]);

      connection.release();

      res.json({
        message: ativo ? 'Usuário ativado' : 'Usuário bloqueado',
      });
    } catch (error) {
      console.error('Erro ao atualizar usuário:', error);
      res.status(500).json({ message: 'Erro ao atualizar usuário' });
    }
  }

  // Aluno: Ver seu perfil
  static async meuPerfil(req, res) {
    try {
      const connection = await pool.getConnection();

      const [usuarios] = await connection.execute(
        'SELECT id, nome, email, role, ativo FROM usuarios WHERE id = ?',
        [req.user.id]
      );

      connection.release();

      if (usuarios.length === 0) {
        return res.status(404).json({ message: 'Usuário não encontrado' });
      }

      res.json(usuarios[0]);
    } catch (error) {
      console.error('Erro ao buscar perfil:', error);
      res.status(500).json({ message: 'Erro ao buscar perfil' });
    }
  }
}

module.exports = UsuarioController;
