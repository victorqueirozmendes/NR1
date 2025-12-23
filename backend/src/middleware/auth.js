import { verifyToken } from '../config/jwt.js';

export function authMiddleware(req, res, next) {
  const token = req.headers.authorization?.split(' ')[1];

  if (!token) {
    return res.status(401).json({ message: 'Token não fornecido' });
  }

  const decoded = verifyToken(token);
  if (!decoded) {
    return res.status(401).json({ message: 'Token inválido ou expirado' });
  }

  req.user = decoded;
  next();
}

export function adminMiddleware(req, res, next) {
  if (req.user.role !== 'admin') {
    return res.status(403).json({ message: 'Acesso negado. Apenas administradores.' });
  }
  next();
}

export function alunoMiddleware(req, res, next) {
  if (req.user.role !== 'aluno' && req.user.role !== 'admin') {
    return res.status(403).json({ message: 'Acesso negado. Apenas alunos.' });
  }
  next();
}
