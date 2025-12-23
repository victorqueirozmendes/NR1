import express from 'express';
import {
  getCourses,
  getCourseById,
  createCourse,
  updateCourse,
  deleteCourse,
  grantAccess,
  revokeAccess,
  getStudentCourses
} from '../controllers/courseController.js';
import { authMiddleware, adminMiddleware, alunoMiddleware } from '../middleware/auth.js';

const router = express.Router();

// Rotas públicas
router.get('/', getCourses);
router.get('/:id', getCourseById);

// Rotas de aluno
router.get('/my-courses', authMiddleware, alunoMiddleware, getStudentCourses);

// Rotas de admin
router.post('/', authMiddleware, adminMiddleware, createCourse);
router.put('/:id', authMiddleware, adminMiddleware, updateCourse);
router.delete('/:id', authMiddleware, adminMiddleware, deleteCourse);
router.post('/grant-access', authMiddleware, adminMiddleware, grantAccess);
router.post('/revoke-access', authMiddleware, adminMiddleware, revokeAccess);

export default router;
