const pool = require('../config/database');

class AulaController {
  // Admin: Criar aula
  static async criar(req, res) {
    try {
      const { moduloId, titulo, descricao, videoUrl, ordem } = req.body;

      const connection = await pool.getConnection();

      const [result] = await connection.execute(
        'INSERT INTO aulas (modulo_id, titulo, descricao, video_url, ordem) VALUES (?, ?, ?, ?, ?)',
        [moduloId, titulo, descricao, videoUrl, ordem || 1]
      );

      connection.release();

      res.status(201).json({
        message: 'Aula criada',
        id: result.insertId,
      });
    } catch (error) {
      console.error('Erro ao criar aula:', error);
      res.status(500).json({ message: 'Erro ao criar aula' });
    }
  }

  // Listar aulas de um módulo
  static async listarPorModulo(req, res) {
    try {
      const { moduloId } = req.params;

      const connection = await pool.getConnection();

      const [aulas] = await connection.execute(
        'SELECT id, titulo, descricao, video_url, ordem FROM aulas WHERE modulo_id = ? ORDER BY ordem ASC',
        [moduloId]
      );

      connection.release();

      res.json(aulas);
    } catch (error) {
      console.error('Erro ao listar aulas:', error);
      res.status(500).json({ message: 'Erro ao listar aulas' });
    }
  }

  // Aluno: Visualizar aula
  static async visualizar(req, res) {
    try {
      const { id } = req.params;

      const connection = await pool.getConnection();

      const [aulas] = await connection.execute(
        'SELECT id, titulo, descricao, video_url FROM aulas WHERE id = ?',
        [id]
      );

      connection.release();

      if (aulas.length === 0) {
        return res.status(404).json({ message: 'Aula não encontrada' });
      }

      res.json(aulas[0]);
    } catch (error) {
      console.error('Erro ao visualizar aula:', error);
      res.status(500).json({ message: 'Erro ao visualizar aula' });
    }
  }

  // Admin: Editar aula
  static async editar(req, res) {
    try {
      const { id } = req.params;
      const { titulo, descricao, videoUrl, ordem } = req.body;

      const connection = await pool.getConnection();

      await connection.execute(
        'UPDATE aulas SET titulo = ?, descricao = ?, video_url = ?, ordem = ? WHERE id = ?',
        [titulo, descricao, videoUrl, ordem, id]
      );

      connection.release();

      res.json({ message: 'Aula atualizada' });
    } catch (error) {
      console.error('Erro ao editar aula:', error);
      res.status(500).json({ message: 'Erro ao editar aula' });
    }
  }

  // Admin: Deletar aula
  static async deletar(req, res) {
    try {
      const { id } = req.params;

      const connection = await pool.getConnection();

      await connection.execute('DELETE FROM aulas WHERE id = ?', [id]);

      connection.release();

      res.json({ message: 'Aula deletada' });
    } catch (error) {
      console.error('Erro ao deletar aula:', error);
      res.status(500).json({ message: 'Erro ao deletar aula' });
    }
  }
}

module.exports = AulaController;
