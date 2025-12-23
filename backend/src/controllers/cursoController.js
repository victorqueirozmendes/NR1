const pool = require('../config/database');

class CursoController {
  // Admin: Criar curso
  static async criar(req, res) {
    try {
      const { nome, descricao } = req.body;

      const connection = await pool.getConnection();

      const [result] = await connection.execute(
        'INSERT INTO cursos (nome, descricao) VALUES (?, ?)',
        [nome, descricao]
      );

      connection.release();

      res.status(201).json({
        message: 'Curso criado',
        id: result.insertId,
      });
    } catch (error) {
      console.error('Erro ao criar curso:', error);
      res.status(500).json({ message: 'Erro ao criar curso' });
    }
  }

  // Admin: Listar todos os cursos
  static async listar(req, res) {
    try {
      const connection = await pool.getConnection();

      const [cursos] = await connection.execute(
        'SELECT id, nome, descricao, created_at FROM cursos ORDER BY created_at DESC'
      );

      connection.release();

      res.json(cursos);
    } catch (error) {
      console.error('Erro ao listar cursos:', error);
      res.status(500).json({ message: 'Erro ao listar cursos' });
    }
  }

  // Admin: Editar curso
  static async editar(req, res) {
    try {
      const { id } = req.params;
      const { nome, descricao } = req.body;

      const connection = await pool.getConnection();

      await connection.execute(
        'UPDATE cursos SET nome = ?, descricao = ? WHERE id = ?',
        [nome, descricao, id]
      );

      connection.release();

      res.json({ message: 'Curso atualizado' });
    } catch (error) {
      console.error('Erro ao editar curso:', error);
      res.status(500).json({ message: 'Erro ao editar curso' });
    }
  }

  // Aluno: Ver cursos que tem acesso
  static async meusCursos(req, res) {
    try {
      const connection = await pool.getConnection();

      const [cursos] = await connection.execute(
        `SELECT DISTINCT c.id, c.nome, c.descricao 
         FROM cursos c
         WHERE c.id IN (
           SELECT DISTINCT a.curso_id 
           FROM acessos a 
           WHERE a.usuario_id = ? AND a.ativo = true
         )
         ORDER BY c.created_at DESC`,
        [req.user.id]
      );

      connection.release();

      res.json(cursos);
    } catch (error) {
      console.error('Erro ao listar meus cursos:', error);
      res.status(500).json({ message: 'Erro ao listar cursos' });
    }
  }

  // Admin: Liberar curso para aluno
  static async liberarParaAluno(req, res) {
    try {
      const { cursoId, usuarioId } = req.body;

      const connection = await pool.getConnection();

      const [existing] = await connection.execute(
        'SELECT id FROM acessos WHERE usuario_id = ? AND curso_id = ?',
        [usuarioId, cursoId]
      );

      if (existing.length > 0) {
        connection.release();
        return res.status(400).json({ message: 'Acesso já existe' });
      }

      await connection.execute(
        'INSERT INTO acessos (usuario_id, curso_id, ativo) VALUES (?, ?, ?)',
        [usuarioId, cursoId, true]
      );

      connection.release();

      res.json({ message: 'Acesso liberado' });
    } catch (error) {
      console.error('Erro ao liberar acesso:', error);
      res.status(500).json({ message: 'Erro ao liberar acesso' });
    }
  }

  // Admin: Bloquear acesso
  static async bloquearAcesso(req, res) {
    try {
      const { usuarioId, cursoId } = req.body;

      const connection = await pool.getConnection();

      await connection.execute(
        'UPDATE acessos SET ativo = ? WHERE usuario_id = ? AND curso_id = ?',
        [false, usuarioId, cursoId]
      );

      connection.release();

      res.json({ message: 'Acesso bloqueado' });
    } catch (error) {
      console.error('Erro ao bloquear acesso:', error);
      res.status(500).json({ message: 'Erro ao bloquear acesso' });
    }
  }
}

module.exports = CursoController;
