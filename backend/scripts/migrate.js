const pool = require('../src/config/database');

const createTables = async () => {
  const connection = await pool.getConnection();

  try {
    console.log('Criando tabelas...');

    // Tabela usuarios
    await connection.execute(`
      CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        senha VARCHAR(255) NOT NULL,
        role ENUM('admin', 'aluno') DEFAULT 'aluno',
        ativo BOOLEAN DEFAULT true,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      )
    `);
    console.log('✓ Tabela usuarios criada');

    // Tabela cursos
    await connection.execute(`
      CREATE TABLE IF NOT EXISTS cursos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        descricao TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      )
    `);
    console.log('✓ Tabela cursos criada');

    // Tabela modulos
    await connection.execute(`
      CREATE TABLE IF NOT EXISTS modulos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        curso_id INT NOT NULL,
        nome VARCHAR(255) NOT NULL,
        ordem INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
      )
    `);
    console.log('✓ Tabela modulos criada');

    // Tabela aulas
    await connection.execute(`
      CREATE TABLE IF NOT EXISTS aulas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        modulo_id INT NOT NULL,
        titulo VARCHAR(255) NOT NULL,
        descricao TEXT,
        video_url VARCHAR(255),
        ordem INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (modulo_id) REFERENCES modulos(id) ON DELETE CASCADE
      )
    `);
    console.log('✓ Tabela aulas criada');

    // Tabela materiais
    await connection.execute(`
      CREATE TABLE IF NOT EXISTS materiais (
        id INT AUTO_INCREMENT PRIMARY KEY,
        aula_id INT NOT NULL,
        tipo ENUM('pdf', 'link', 'arquivo') NOT NULL,
        url VARCHAR(255) NOT NULL,
        nome VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (aula_id) REFERENCES aulas(id) ON DELETE CASCADE
      )
    `);
    console.log('✓ Tabela materiais criada');

    // Tabela progresso
    await connection.execute(`
      CREATE TABLE IF NOT EXISTS progresso (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        aula_id INT NOT NULL,
        concluida BOOLEAN DEFAULT false,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_usuario_aula (usuario_id, aula_id),
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
        FOREIGN KEY (aula_id) REFERENCES aulas(id) ON DELETE CASCADE
      )
    `);
    console.log('✓ Tabela progresso criada');

    // Tabela acessos (controle de acesso aluno-curso)
    await connection.execute(`
      CREATE TABLE IF NOT EXISTS acessos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        curso_id INT NOT NULL,
        ativo BOOLEAN DEFAULT true,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_usuario_curso (usuario_id, curso_id),
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
        FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
      )
    `);
    console.log('✓ Tabela acessos criada');

    console.log('✅ Todas as tabelas foram criadas com sucesso!');
    process.exit(0);
  } catch (error) {
    console.error('❌ Erro ao criar tabelas:', error);
    process.exit(1);
  } finally {
    connection.release();
  }
};

createTables();
