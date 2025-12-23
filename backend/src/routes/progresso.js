const express = require('express');
const ProgressoController = require('../controllers/progressoController');
const { authMiddleware, adminMiddleware } = require('../middleware/auth');

const router = express.Router();

router.post('/marcar-concluida', authMiddleware, ProgressoController.marcarConcluida);
router.get('/curso/:cursoId', authMiddleware, ProgressoController.meuProgresso);
router.get('/aluno/:usuarioId/curso/:cursoId', authMiddleware, adminMiddleware, ProgressoController.progressoAluno);

module.exports = router;
