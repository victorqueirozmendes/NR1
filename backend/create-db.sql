-- Script para criar banco de dados e tabelas
CREATE DATABASE IF NOT EXISTS nr1_ead;
USE nr1_ead;

-- Tabela usuarios
CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  senha VARCHAR(255) NOT NULL,
  role ENUM('admin', 'aluno') DEFAULT 'aluno',
  ativo BOOLEAN DEFAULT true,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela cursos
CREATE TABLE IF NOT EXISTS cursos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  descricao TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela modulos
CREATE TABLE IF NOT EXISTS modulos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  curso_id INT NOT NULL,
  nome VARCHAR(255) NOT NULL,
  ordem INT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
);

-- Tabela aulas
CREATE TABLE IF NOT EXISTS aulas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  modulo_id INT NOT NULL,
  titulo VARCHAR(255) NOT NULL,
  descricao TEXT,
  video_url VARCHAR(255),
  ordem INT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (modulo_id) REFERENCES modulos(id) ON DELETE CASCADE
);

-- Tabela materiais
CREATE TABLE IF NOT EXISTS materiais (
  id INT AUTO_INCREMENT PRIMARY KEY,
  aula_id INT NOT NULL,
  tipo ENUM('pdf', 'link', 'arquivo') NOT NULL,
  url VARCHAR(255) NOT NULL,
  nome VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (aula_id) REFERENCES aulas(id) ON DELETE CASCADE
);

-- Tabela progresso
CREATE TABLE IF NOT EXISTS progresso (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  aula_id INT NOT NULL,
  concluida BOOLEAN DEFAULT false,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_usuario_aula (usuario_id, aula_id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (aula_id) REFERENCES aulas(id) ON DELETE CASCADE
);

-- Tabela acessos
CREATE TABLE IF NOT EXISTS acessos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  curso_id INT NOT NULL,
  ativo BOOLEAN DEFAULT true,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_usuario_curso (usuario_id, curso_id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
);

-- Inserir usuário admin padrão (senha: admin123)
-- Hash: $2a$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36gZvQm6
INSERT INTO usuarios (nome, email, senha, role, ativo) VALUES 
('Admin NR1', 'admin@nr1.com', '$2a$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36gZvQm6', 'admin', true)
ON DUPLICATE KEY UPDATE id=id;
