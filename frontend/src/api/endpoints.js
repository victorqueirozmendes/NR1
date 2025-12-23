import api from './api';

export const authAPI = {
  register: (nome, email, senha) =>
    api.post('/auth/register', { nome, email, senha }),

  login: (email, senha) =>
    api.post('/auth/login', { email, senha }),

  verify: () =>
    api.get('/auth/verify'),
};

export const usuariosAPI = {
  criar: (nome, email, senha, role) =>
    api.post('/usuarios', { nome, email, senha, role }),

  listar: () =>
    api.get('/usuarios'),

  toggleAtivo: (id, ativo) =>
    api.patch(`/usuarios/${id}/toggle-ativo`, { ativo }),

  meuPerfil: () =>
    api.get('/usuarios/me'),
};

export const cursosAPI = {
  criar: (nome, descricao) =>
    api.post('/cursos', { nome, descricao }),

  listar: () =>
    api.get('/cursos/admin/list'),

  editar: (id, nome, descricao) =>
    api.patch(`/cursos/${id}`, { nome, descricao }),

  meusCursos: () =>
    api.get('/cursos/meus'),

  liberarAcesso: (usuarioId, cursoId) =>
    api.post('/cursos/liberar-acesso', { usuarioId, cursoId }),

  bloquearAcesso: (usuarioId, cursoId) =>
    api.post('/cursos/bloquear-acesso', { usuarioId, cursoId }),
};

export const modulosAPI = {
  criar: (cursoId, nome, ordem) =>
    api.post('/modulos', { cursoId, nome, ordem }),

  listarPorCurso: (cursoId) =>
    api.get(`/modulos/curso/${cursoId}`),

  editar: (id, nome, ordem) =>
    api.patch(`/modulos/${id}`, { nome, ordem }),

  deletar: (id) =>
    api.delete(`/modulos/${id}`),
};

export const aulasAPI = {
  criar: (moduloId, titulo, descricao, videoUrl, ordem) =>
    api.post('/aulas', { moduloId, titulo, descricao, videoUrl, ordem }),

  listarPorModulo: (moduloId) =>
    api.get(`/aulas/modulo/${moduloId}`),

  visualizar: (id) =>
    api.get(`/aulas/${id}`),

  editar: (id, titulo, descricao, videoUrl, ordem) =>
    api.patch(`/aulas/${id}`, { titulo, descricao, videoUrl, ordem }),

  deletar: (id) =>
    api.delete(`/aulas/${id}`),
};

export const materiaisAPI = {
  criar: (aulaId, tipo, url, nome) =>
    api.post('/materiais', { aulaId, tipo, url, nome }),

  listarPorAula: (aulaId) =>
    api.get(`/materiais/aula/${aulaId}`),

  deletar: (id) =>
    api.delete(`/materiais/${id}`),
};

export const progressoAPI = {
  marcarConcluida: (aulaId) =>
    api.post('/progresso/marcar-concluida', { aulaId }),

  meuProgresso: (cursoId) =>
    api.get(`/progresso/curso/${cursoId}`),

  progressoAluno: (usuarioId, cursoId) =>
    api.get(`/progresso/aluno/${usuarioId}/curso/${cursoId}`),
};
