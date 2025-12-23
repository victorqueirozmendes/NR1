const pool = require('../config/database');

class MaterialController {
  // Admin: Adicionar material à aula
  static async criar(req, res) {
    try {
      const { aulaId, tipo, url, nome } = req.body;

      const connection = await pool.getConnection();

      const [result] = await connection.execute(
        'INSERT INTO materiais (aula_id, tipo, url, nome) VALUES (?, ?, ?, ?)',
        [aulaId, tipo, url, nome]
      );

      connection.release();

      res.status(201).json({
        message: 'Material adicionado',
        id: result.insertId,
      });
    } catch (error) {
      console.error('Erro ao adicionar material:', error);
      res.status(500).json({ message: 'Erro ao adicionar material' });
    }
  }

  // Listar materiais de uma aula
  static async listarPorAula(req, res) {
    try {
      const { aulaId } = req.params;

      const connection = await pool.getConnection();

      const [materiais] = await connection.execute(
        'SELECT id, tipo, url, nome FROM materiais WHERE aula_id = ? ORDER BY created_at ASC',
        [aulaId]
      );

      connection.release();

      res.json(materiais);
    } catch (error) {
      console.error('Erro ao listar materiais:', error);
      res.status(500).json({ message: 'Erro ao listar materiais' });
    }
  }

  // Admin: Deletar material
  static async deletar(req, res) {
    try {
      const { id } = req.params;

      const connection = await pool.getConnection();

      await connection.execute('DELETE FROM materiais WHERE id = ?', [id]);

      connection.release();

      res.json({ message: 'Material deletado' });
    } catch (error) {
      console.error('Erro ao deletar material:', error);
      res.status(500).json({ message: 'Erro ao deletar material' });
    }
  }
}

module.exports = MaterialController;
