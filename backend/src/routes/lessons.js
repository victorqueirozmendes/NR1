import express from 'express';
import {
  getLessons,
  getLessonById,
  createLesson,
  updateLesson,
  deleteLesson,
  getModules,
  createModule,
  addMaterial
} from '../controllers/lessonController.js';
import { authMiddleware, adminMiddleware, alunoMiddleware } from '../middleware/auth.js';

const router = express.Router();

// Rotas públicas
router.get('/', getLessons);
router.get('/:id', getLessonById);

// Rotas de módulos
router.get('/modules/list', getModules);
router.post('/modules', authMiddleware, adminMiddleware, createModule);

// Rotas de aulas
router.post('/', authMiddleware, adminMiddleware, createLesson);
router.put('/:id', authMiddleware, adminMiddleware, updateLesson);
router.delete('/:id', authMiddleware, adminMiddleware, deleteLesson);

// Rotas de materiais
router.post('/materials', authMiddleware, adminMiddleware, addMaterial);

export default router;
