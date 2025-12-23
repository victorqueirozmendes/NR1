const express = require('express');
const AulaController = require('../controllers/aulaController');
const { authMiddleware, adminMiddleware } = require('../middleware/auth');

const router = express.Router();

router.post('/', authMiddleware, adminMiddleware, AulaController.criar);
router.get('/modulo/:moduloId', authMiddleware, AulaController.listarPorModulo);
router.get('/:id', authMiddleware, AulaController.visualizar);
router.patch('/:id', authMiddleware, adminMiddleware, AulaController.editar);
router.delete('/:id', authMiddleware, adminMiddleware, AulaController.deletar);

module.exports = router;
