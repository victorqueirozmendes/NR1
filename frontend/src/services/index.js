import api from './api';

export const authService = {
  register: async (nome, email, senha) => {
    const response = await api.post('/auth/register', { nome, email, senha });
    return response.data;
  },

  login: async (email, senha) => {
    const response = await api.post('/auth/login', { email, senha });
    return response.data;
  },

  me: async () => {
    const response = await api.get('/auth/me');
    return response.data;
  },

  logout: () => {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
  }
};

export const courseService = {
  getCourses: async () => {
    const response = await api.get('/courses');
    return response.data;
  },

  getCourseById: async (id) => {
    const response = await api.get(`/courses/${id}`);
    return response.data;
  },

  getStudentCourses: async () => {
    const response = await api.get('/courses/my-courses');
    return response.data;
  },

  createCourse: async (nome, descricao) => {
    const response = await api.post('/courses', { nome, descricao });
    return response.data;
  },

  updateCourse: async (id, nome, descricao) => {
    const response = await api.put(`/courses/${id}`, { nome, descricao });
    return response.data;
  },

  deleteCourse: async (id) => {
    const response = await api.delete(`/courses/${id}`);
    return response.data;
  },

  grantAccess: async (userId, courseId) => {
    const response = await api.post('/courses/grant-access', { userId, courseId });
    return response.data;
  },

  revokeAccess: async (userId, courseId) => {
    const response = await api.post('/courses/revoke-access', { userId, courseId });
    return response.data;
  }
};

export const lessonService = {
  getLessons: async (moduleId) => {
    const response = await api.get('/lessons', { params: { moduleId } });
    return response.data;
  },

  getLessonById: async (id) => {
    const response = await api.get(`/lessons/${id}`);
    return response.data;
  },

  createLesson: async (moduleId, titulo, descricao, video_url, ordem) => {
    const response = await api.post('/lessons', { moduleId, titulo, descricao, video_url, ordem });
    return response.data;
  },

  updateLesson: async (id, titulo, descricao, video_url, ordem) => {
    const response = await api.put(`/lessons/${id}`, { titulo, descricao, video_url, ordem });
    return response.data;
  },

  deleteLesson: async (id) => {
    const response = await api.delete(`/lessons/${id}`);
    return response.data;
  },

  getModules: async (courseId) => {
    const response = await api.get('/lessons/modules/list', { params: { courseId } });
    return response.data;
  },

  createModule: async (courseId, nome, ordem) => {
    const response = await api.post('/lessons/modules', { courseId, nome, ordem });
    return response.data;
  },

  addMaterial: async (lessonId, tipo, titulo, url) => {
    const response = await api.post('/lessons/materials', { lessonId, tipo, titulo, url });
    return response.data;
  }
};

export const progressService = {
  getProgress: async () => {
    const response = await api.get('/progress');
    return response.data;
  },

  getCourseProgress: async (courseId) => {
    const response = await api.get(`/progress/course/${courseId}`);
    return response.data;
  },

  markLessonComplete: async (lessonId) => {
    const response = await api.post('/progress/mark-complete', { lessonId });
    return response.data;
  },

  getStudentProgress: async (studentId) => {
    const response = await api.get(`/progress/student/${studentId}`);
    return response.data;
  }
};

export const userService = {
  getUsers: async () => {
    const response = await api.get('/users');
    return response.data;
  },

  createUser: async (nome, email, senha, role) => {
    const response = await api.post('/users', { nome, email, senha, role });
    return response.data;
  },

  updateUser: async (id, nome, email, role, ativo) => {
    const response = await api.put(`/users/${id}`, { nome, email, role, ativo });
    return response.data;
  },

  deleteUser: async (id) => {
    const response = await api.delete(`/users/${id}`);
    return response.data;
  },

  deactivateUser: async (id) => {
    const response = await api.patch(`/users/${id}/deactivate`);
    return response.data;
  }
};
