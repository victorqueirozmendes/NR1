const pool = require('../config/database');

class ModuloController {
  // Admin: Criar módulo
  static async criar(req, res) {
    try {
      const { cursoId, nome, ordem } = req.body;

      const connection = await pool.getConnection();

      const [result] = await connection.execute(
        'INSERT INTO modulos (curso_id, nome, ordem) VALUES (?, ?, ?)',
        [cursoId, nome, ordem || 1]
      );

      connection.release();

      res.status(201).json({
        message: 'Módulo criado',
        id: result.insertId,
      });
    } catch (error) {
      console.error('Erro ao criar módulo:', error);
      res.status(500).json({ message: 'Erro ao criar módulo' });
    }
  }

  // Listar módulos de um curso
  static async listarPorCurso(req, res) {
    try {
      const { cursoId } = req.params;

      const connection = await pool.getConnection();

      const [modulos] = await connection.execute(
        'SELECT id, nome, ordem FROM modulos WHERE curso_id = ? ORDER BY ordem ASC',
        [cursoId]
      );

      connection.release();

      res.json(modulos);
    } catch (error) {
      console.error('Erro ao listar módulos:', error);
      res.status(500).json({ message: 'Erro ao listar módulos' });
    }
  }

  // Admin: Editar módulo
  static async editar(req, res) {
    try {
      const { id } = req.params;
      const { nome, ordem } = req.body;

      const connection = await pool.getConnection();

      await connection.execute(
        'UPDATE modulos SET nome = ?, ordem = ? WHERE id = ?',
        [nome, ordem, id]
      );

      connection.release();

      res.json({ message: 'Módulo atualizado' });
    } catch (error) {
      console.error('Erro ao editar módulo:', error);
      res.status(500).json({ message: 'Erro ao editar módulo' });
    }
  }

  // Admin: Deletar módulo
  static async deletar(req, res) {
    try {
      const { id } = req.params;

      const connection = await pool.getConnection();

      await connection.execute('DELETE FROM modulos WHERE id = ?', [id]);

      connection.release();

      res.json({ message: 'Módulo deletado' });
    } catch (error) {
      console.error('Erro ao deletar módulo:', error);
      res.status(500).json({ message: 'Erro ao deletar módulo' });
    }
  }
}

module.exports = ModuloController;
