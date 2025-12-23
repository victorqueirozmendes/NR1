const express = require('express');
const ModuloController = require('../controllers/moduloController');
const { authMiddleware, adminMiddleware } = require('../middleware/auth');

const router = express.Router();

router.post('/', authMiddleware, adminMiddleware, ModuloController.criar);
router.get('/curso/:cursoId', authMiddleware, ModuloController.listarPorCurso);
router.patch('/:id', authMiddleware, adminMiddleware, ModuloController.editar);
router.delete('/:id', authMiddleware, adminMiddleware, ModuloController.deletar);

module.exports = router;
