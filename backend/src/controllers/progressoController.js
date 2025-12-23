const pool = require('../config/database');

class ProgressoController {
  // Aluno: Marcar aula como concluída
  static async marcarConcluida(req, res) {
    try {
      const { aulaId } = req.body;
      const usuarioId = req.user.id;

      const connection = await pool.getConnection();

      const [existing] = await connection.execute(
        'SELECT id FROM progresso WHERE usuario_id = ? AND aula_id = ?',
        [usuarioId, aulaId]
      );

      if (existing.length > 0) {
        await connection.execute(
          'UPDATE progresso SET concluida = true WHERE usuario_id = ? AND aula_id = ?',
          [usuarioId, aulaId]
        );
      } else {
        await connection.execute(
          'INSERT INTO progresso (usuario_id, aula_id, concluida) VALUES (?, ?, ?)',
          [usuarioId, aulaId, true]
        );
      }

      connection.release();

      res.json({ message: 'Aula marcada como concluída' });
    } catch (error) {
      console.error('Erro ao marcar aula como concluída:', error);
      res.status(500).json({ message: 'Erro ao atualizar progresso' });
    }
  }

  // Aluno: Ver seu progresso em um curso
  static async meuProgresso(req, res) {
    try {
      const { cursoId } = req.params;
      const usuarioId = req.user.id;

      const connection = await pool.getConnection();

      // Total de aulas do curso
      const [totalAulas] = await connection.execute(
        `SELECT COUNT(a.id) as total 
         FROM aulas a
         JOIN modulos m ON a.modulo_id = m.id
         WHERE m.curso_id = ?`,
        [cursoId]
      );

      // Aulas concluídas
      const [aulasConc] = await connection.execute(
        `SELECT COUNT(DISTINCT p.aula_id) as concluidas
         FROM progresso p
         JOIN aulas a ON p.aula_id = a.id
         JOIN modulos m ON a.modulo_id = m.id
         WHERE m.curso_id = ? AND p.usuario_id = ? AND p.concluida = true`,
        [cursoId, usuarioId]
      );

      connection.release();

      const total = totalAulas[0].total || 0;
      const concluidas = aulasConc[0].concluidas || 0;
      const percentual = total > 0 ? Math.round((concluidas / total) * 100) : 0;

      res.json({
        total,
        concluidas,
        percentual,
      });
    } catch (error) {
      console.error('Erro ao calcular progresso:', error);
      res.status(500).json({ message: 'Erro ao calcular progresso' });
    }
  }

  // Admin: Ver progresso de um aluno em um curso
  static async progressoAluno(req, res) {
    try {
      const { usuarioId, cursoId } = req.params;

      const connection = await pool.getConnection();

      const [totalAulas] = await connection.execute(
        `SELECT COUNT(a.id) as total 
         FROM aulas a
         JOIN modulos m ON a.modulo_id = m.id
         WHERE m.curso_id = ?`,
        [cursoId]
      );

      const [aulasConc] = await connection.execute(
        `SELECT COUNT(DISTINCT p.aula_id) as concluidas
         FROM progresso p
         JOIN aulas a ON p.aula_id = a.id
         JOIN modulos m ON a.modulo_id = m.id
         WHERE m.curso_id = ? AND p.usuario_id = ? AND p.concluida = true`,
        [cursoId, usuarioId]
      );

      connection.release();

      const total = totalAulas[0].total || 0;
      const concluidas = aulasConc[0].concluidas || 0;
      const percentual = total > 0 ? Math.round((concluidas / total) * 100) : 0;

      res.json({
        total,
        concluidas,
        percentual,
      });
    } catch (error) {
      console.error('Erro ao buscar progresso:', error);
      res.status(500).json({ message: 'Erro ao buscar progresso' });
    }
  }
}

module.exports = ProgressoController;
