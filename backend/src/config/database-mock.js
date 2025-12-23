// Database simulado em memória (para demonstração)
// Em produção, use MySQL real

// Armazenamento em memória
const db = {
  usuarios: [
    {
      id: 1,
      nome: 'Admin NR1',
      email: 'admin@nr1.com',
      senha: '$2a$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36gZvQm6', // admin123
      role: 'admin',
      ativo: true,
      created_at: new Date()
    }
  ],
  cursos: [],
  modulos: [],
  aulas: [],
  materiais: [],
  progresso: [],
  acessos: [],
  nextIds: {
    usuarios: 2,
    cursos: 1,
    modulos: 1,
    aulas: 1,
    materiais: 1,
    progresso: 1,
    acessos: 1
  }
};

// Exportar como pool simulado
class MockPool {
  async getConnection() {
    return new MockConnection();
  }
}

class MockConnection {
  async execute(sql, params = []) {
    // Simular respostas para diferentes queries
    if (sql.includes('SELECT') && sql.includes('usuarios')) {
      return [db.usuarios.filter(u => !u.deleted), []];
    }
    if (sql.includes('INSERT INTO usuarios')) {
      const usuario = {
        id: db.nextIds.usuarios++,
        nome: params[0],
        email: params[1],
        senha: params[2],
        role: params[3] || 'aluno',
        ativo: true,
        created_at: new Date()
      };
      db.usuarios.push(usuario);
      return [{ insertId: usuario.id }, []];
    }
    if (sql.includes('INSERT INTO cursos')) {
      const curso = {
        id: db.nextIds.cursos++,
        nome: params[0],
        descricao: params[1],
        created_at: new Date()
      };
      db.cursos.push(curso);
      return [{ insertId: curso.id }, []];
    }
    if (sql.includes('SELECT') && sql.includes('cursos')) {
      return [db.cursos.filter(c => !c.deleted), []];
    }
    return [[], []];
  }

  release() {
    // Mock
  }
}

export default new MockPool();
