import { query, queryOne } from '../config/database.js';

export async function getCourses(req, res) {
  try {
    const courses = await query('SELECT * FROM courses');
    res.json(courses);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao buscar cursos' });
  }
}

export async function getCourseById(req, res) {
  try {
    const { id } = req.params;
    const course = await queryOne('SELECT * FROM courses WHERE id = ?', [id]);
    
    if (!course) {
      return res.status(404).json({ message: 'Curso não encontrado' });
    }

    res.json(course);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao buscar curso' });
  }
}

export async function createCourse(req, res) {
  try {
    const { nome, descricao } = req.body;

    if (!nome) {
      return res.status(400).json({ message: 'Nome do curso é obrigatório' });
    }

    const result = await query(
      'INSERT INTO courses (nome, descricao) VALUES (?, ?)',
      [nome, descricao || null]
    );

    const course = await queryOne('SELECT * FROM courses WHERE id = ?', [result.insertId]);
    res.status(201).json(course);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao criar curso' });
  }
}

export async function updateCourse(req, res) {
  try {
    const { id } = req.params;
    const { nome, descricao } = req.body;

    const course = await queryOne('SELECT * FROM courses WHERE id = ?', [id]);
    if (!course) {
      return res.status(404).json({ message: 'Curso não encontrado' });
    }

    await query(
      'UPDATE courses SET nome = ?, descricao = ? WHERE id = ?',
      [nome || course.nome, descricao || course.descricao, id]
    );

    const updatedCourse = await queryOne('SELECT * FROM courses WHERE id = ?', [id]);
    res.json(updatedCourse);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao atualizar curso' });
  }
}

export async function deleteCourse(req, res) {
  try {
    const { id } = req.params;

    const course = await queryOne('SELECT * FROM courses WHERE id = ?', [id]);
    if (!course) {
      return res.status(404).json({ message: 'Curso não encontrado' });
    }

    await query('DELETE FROM courses WHERE id = ?', [id]);
    res.json({ message: 'Curso deletado com sucesso' });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao deletar curso' });
  }
}

// Liberar acesso a curso para um aluno
export async function grantAccess(req, res) {
  try {
    const { userId, courseId } = req.body;

    const result = await query(
      'INSERT INTO course_access (user_id, course_id, ativo) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE ativo = ?',
      [userId, courseId, true, true]
    );

    res.json({ message: 'Acesso liberado com sucesso' });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao liberar acesso' });
  }
}

// Bloquear acesso a curso
export async function revokeAccess(req, res) {
  try {
    const { userId, courseId } = req.body;

    await query(
      'UPDATE course_access SET ativo = ? WHERE user_id = ? AND course_id = ?',
      [false, userId, courseId]
    );

    res.json({ message: 'Acesso revogado com sucesso' });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao revogar acesso' });
  }
}

// Listar cursos do aluno (apenas os que tem acesso)
export async function getStudentCourses(req, res) {
  try {
    const userId = req.user.id;

    const courses = await query(`
      SELECT c.* FROM courses c
      INNER JOIN course_access ca ON c.id = ca.course_id
      WHERE ca.user_id = ? AND ca.ativo = true
    `, [userId]);

    res.json(courses);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao buscar cursos do aluno' });
  }
}
