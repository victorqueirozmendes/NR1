const express = require('express');
const UsuarioController = require('../controllers/usuarioController');
const { authMiddleware, adminMiddleware } = require('../middleware/auth');

const router = express.Router();

// Admin routes
router.post('/', authMiddleware, adminMiddleware, UsuarioController.createUsuario);
router.get('/', authMiddleware, adminMiddleware, UsuarioController.listarUsuarios);
router.patch('/:id/toggle-ativo', authMiddleware, adminMiddleware, UsuarioController.toggleAtivoUsuario);

// Aluno routes
router.get('/me', authMiddleware, UsuarioController.meuPerfil);

module.exports = router;
