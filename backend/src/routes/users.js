import express from 'express';
import {
  getUsers,
  createUser,
  updateUser,
  deleteUser,
  deactivateUser
} from '../controllers/userController.js';
import { authMiddleware, adminMiddleware } from '../middleware/auth.js';

const router = express.Router();

// Todas as rotas de usuário são admin-only
router.get('/', authMiddleware, adminMiddleware, getUsers);
router.post('/', authMiddleware, adminMiddleware, createUser);
router.put('/:id', authMiddleware, adminMiddleware, updateUser);
router.delete('/:id', authMiddleware, adminMiddleware, deleteUser);
router.patch('/:id/deactivate', authMiddleware, adminMiddleware, deactivateUser);

export default router;
