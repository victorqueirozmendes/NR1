import { query, queryOne } from '../config/database.js';

export async function getLessons(req, res) {
  try {
    const { moduleId } = req.query;
    
    let sql = 'SELECT * FROM lessons';
    let params = [];

    if (moduleId) {
      sql += ' WHERE module_id = ?';
      params.push(moduleId);
    }

    sql += ' ORDER BY ordem ASC';

    const lessons = await query(sql, params);
    res.json(lessons);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao buscar aulas' });
  }
}

export async function getLessonById(req, res) {
  try {
    const { id } = req.params;
    const lesson = await queryOne('SELECT * FROM lessons WHERE id = ?', [id]);
    
    if (!lesson) {
      return res.status(404).json({ message: 'Aula não encontrada' });
    }

    // Buscar materiais da aula
    const materials = await query('SELECT * FROM materials WHERE lesson_id = ?', [id]);
    lesson.materials = materials;

    res.json(lesson);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao buscar aula' });
  }
}

export async function createLesson(req, res) {
  try {
    const { moduleId, titulo, descricao, video_url, ordem } = req.body;

    if (!moduleId || !titulo) {
      return res.status(400).json({ message: 'Módulo e título são obrigatórios' });
    }

    const result = await query(
      'INSERT INTO lessons (module_id, titulo, descricao, video_url, ordem) VALUES (?, ?, ?, ?, ?)',
      [moduleId, titulo, descricao || null, video_url || null, ordem || 0]
    );

    const lesson = await queryOne('SELECT * FROM lessons WHERE id = ?', [result.insertId]);
    res.status(201).json(lesson);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao criar aula' });
  }
}

export async function updateLesson(req, res) {
  try {
    const { id } = req.params;
    const { titulo, descricao, video_url, ordem } = req.body;

    const lesson = await queryOne('SELECT * FROM lessons WHERE id = ?', [id]);
    if (!lesson) {
      return res.status(404).json({ message: 'Aula não encontrada' });
    }

    await query(
      'UPDATE lessons SET titulo = ?, descricao = ?, video_url = ?, ordem = ? WHERE id = ?',
      [titulo || lesson.titulo, descricao || lesson.descricao, video_url || lesson.video_url, ordem ?? lesson.ordem, id]
    );

    const updatedLesson = await queryOne('SELECT * FROM lessons WHERE id = ?', [id]);
    res.json(updatedLesson);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao atualizar aula' });
  }
}

export async function deleteLesson(req, res) {
  try {
    const { id } = req.params;

    const lesson = await queryOne('SELECT * FROM lessons WHERE id = ?', [id]);
    if (!lesson) {
      return res.status(404).json({ message: 'Aula não encontrada' });
    }

    await query('DELETE FROM lessons WHERE id = ?', [id]);
    res.json({ message: 'Aula deletada com sucesso' });
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao deletar aula' });
  }
}

// Módulos
export async function getModules(req, res) {
  try {
    const { courseId } = req.query;
    
    if (!courseId) {
      return res.status(400).json({ message: 'Course ID é obrigatório' });
    }

    const modules = await query(
      'SELECT * FROM modules WHERE course_id = ? ORDER BY ordem ASC',
      [courseId]
    );

    // Para cada módulo, buscar as aulas
    for (let module of modules) {
      const lessons = await query('SELECT * FROM lessons WHERE module_id = ? ORDER BY ordem ASC', [module.id]);
      module.lessons = lessons;
    }

    res.json(modules);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao buscar módulos' });
  }
}

export async function createModule(req, res) {
  try {
    const { courseId, nome, ordem } = req.body;

    if (!courseId || !nome) {
      return res.status(400).json({ message: 'Curso e nome são obrigatórios' });
    }

    const result = await query(
      'INSERT INTO modules (course_id, nome, ordem) VALUES (?, ?, ?)',
      [courseId, nome, ordem || 0]
    );

    const module = await queryOne('SELECT * FROM modules WHERE id = ?', [result.insertId]);
    res.status(201).json(module);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao criar módulo' });
  }
}

export async function addMaterial(req, res) {
  try {
    const { lessonId, tipo, titulo, url } = req.body;

    if (!lessonId || !tipo || !titulo || !url) {
      return res.status(400).json({ message: 'Todos os campos são obrigatórios' });
    }

    const result = await query(
      'INSERT INTO materials (lesson_id, tipo, titulo, url) VALUES (?, ?, ?, ?)',
      [lessonId, tipo, titulo, url]
    );

    const material = await queryOne('SELECT * FROM materials WHERE id = ?', [result.insertId]);
    res.status(201).json(material);
  } catch (error) {
    console.error(error);
    res.status(500).json({ message: 'Erro ao adicionar material' });
  }
}
