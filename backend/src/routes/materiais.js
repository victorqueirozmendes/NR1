const express = require('express');
const MaterialController = require('../controllers/materialController');
const { authMiddleware, adminMiddleware } = require('../middleware/auth');

const router = express.Router();

router.post('/', authMiddleware, adminMiddleware, MaterialController.criar);
router.get('/aula/:aulaId', authMiddleware, MaterialController.listarPorAula);
router.delete('/:id', authMiddleware, adminMiddleware, MaterialController.deletar);

module.exports = router;
