import express from 'express';
import {
  markLessonComplete,
  getProgress,
  getCourseProgress,
  getStudentProgress
} from '../controllers/progressController.js';
import { authMiddleware, adminMiddleware } from '../middleware/auth.js';

const router = express.Router();

// Rotas de aluno
router.post('/mark-complete', authMiddleware, markLessonComplete);
router.get('/', authMiddleware, getProgress);
router.get('/course/:courseId', authMiddleware, getCourseProgress);

// Rotas de admin
router.get('/student/:studentId', authMiddleware, adminMiddleware, getStudentProgress);

export default router;
