const express = require('express');
const CursoController = require('../controllers/cursoController');
const { authMiddleware, adminMiddleware } = require('../middleware/auth');

const router = express.Router();

// Admin routes
router.post('/', authMiddleware, adminMiddleware, CursoController.criar);
router.get('/admin/list', authMiddleware, adminMiddleware, CursoController.listar);
router.patch('/:id', authMiddleware, adminMiddleware, CursoController.editar);
router.post('/liberar-acesso', authMiddleware, adminMiddleware, CursoController.liberarParaAluno);
router.post('/bloquear-acesso', authMiddleware, adminMiddleware, CursoController.bloquearAcesso);

// Aluno routes
router.get('/meus', authMiddleware, CursoController.meusCursos);

module.exports = router;
