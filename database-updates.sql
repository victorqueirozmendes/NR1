-- Adicionar campo YouTube URL na tabela aulas
ALTER TABLE aulas ADD COLUMN youtube_url VARCHAR(255) DEFAULT NULL AFTER conteudo;

-- Criar tabela de controle de acesso por usuário
CREATE TABLE IF NOT EXISTS user_access_control (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    curso_id INT NOT NULL,
    access_level ENUM('completo', 'limitado', 'bloqueado') DEFAULT 'completo',
    pode_ver_materiais BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_course (usuario_id, curso_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
);

-- Criar índices para melhorar performance
CREATE INDEX IF NOT EXISTS idx_youtube_url ON aulas(youtube_url);
CREATE INDEX IF NOT EXISTS idx_user_access ON user_access_control(usuario_id, curso_id);
