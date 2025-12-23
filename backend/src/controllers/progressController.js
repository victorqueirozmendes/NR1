import { query, queryOne } from '../config/database.js';

export async function markLessonComplete(req, res) {
  try {
    const { lessonId } = req.body;
    const userId = req.user.id;

    if (!lessonId) {
      return res.status(400).json({ message: 'Lesson ID é obrigatório' });
    }

    await query(
      'INSERT INTO progress (user_id, lesson_id, concluida) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE concluida = ?',
      [userId, lessonId, true, true]
    );

    res.json({ message: 'Aula marcada como concluída' });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao marcar aula como concluída' });
  }
}

export async function getProgress(req, res) {
  try {
    const userId = req.user.id;

    const progress = await query(
      'SELECT * FROM progress WHERE user_id = ? AND concluida = ?',
      [userId, true]
    );

    res.json(progress);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao buscar progresso' });
  }
}

export async function getCourseProgress(req, res) {
  try {
    const { courseId } = req.params;
    const userId = req.user.id;

    // Buscar todas as aulas do curso
    const allLessons = await query(`
      SELECT l.id FROM lessons l
      INNER JOIN modules m ON l.module_id = m.id
      WHERE m.course_id = ?
    `, [courseId]);

    // Buscar aulas concluídas do aluno
    const completedLessons = await query(`
      SELECT l.id FROM lessons l
      INNER JOIN modules m ON l.module_id = m.id
      INNER JOIN progress p ON l.id = p.lesson_id
      WHERE m.course_id = ? AND p.user_id = ? AND p.concluida = ?
    `, [courseId, userId, true]);

    const totalLessons = allLessons.length;
    const completedCount = completedLessons.length;
    const percentage = totalLessons > 0 ? Math.round((completedCount / totalLessons) * 100) : 0;

    res.json({
      courseId,
      totalLessons,
      completedLessons: completedCount,
      percentage
    });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao buscar progresso do curso' });
  }
}

export async function getStudentProgress(req, res) {
  try {
    const { studentId } = req.params;

    const progress = await query(
      `SELECT p.*, l.titulo as lesson_title, m.nome as module_name, c.nome as course_name
       FROM progress p
       INNER JOIN lessons l ON p.lesson_id = l.id
       INNER JOIN modules m ON l.module_id = m.id
       INNER JOIN courses c ON m.course_id = c.id
       WHERE p.user_id = ?`,
      [studentId]
    );

    res.json(progress);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao buscar progresso do aluno' });
  }
}
