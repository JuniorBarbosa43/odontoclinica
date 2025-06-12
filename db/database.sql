-- Cria o banco de dados se ele não existir
CREATE DATABASE IF NOT EXISTS odontoclinica_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usa o banco de dados criado
USE odontoclinica_db;

-- Tabela para armazenar os dados dos dentistas
CREATE TABLE dentistas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    cro VARCHAR(20) NULL, -- Conselho Regional de Odontologia
    foto_perfil VARCHAR(255) DEFAULT 'default.png',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela para pacientes (será usada nas próximas etapas)
CREATE TABLE pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    data_nascimento DATE NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    observacoes TEXT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela para consultas (será usada nas próximas etapas)
CREATE TABLE consultas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,
    id_dentista INT NOT NULL,
    data_consulta DATETIME NOT NULL,
    procedimento VARCHAR(255) NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    status ENUM('Agendada', 'Realizada', 'Cancelada') DEFAULT 'Agendada',
    observacoes TEXT NULL,
    FOREIGN KEY (id_paciente) REFERENCES pacientes(id),
    FOREIGN KEY (id_dentista) REFERENCES dentistas(id)
);