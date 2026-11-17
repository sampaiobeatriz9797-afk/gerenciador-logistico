
CREATE DATABASE IF NOT EXISTS gerenciador_logistico
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE gerenciador_logistico;

-- Tabela de produtos
CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    descricao TEXT,
    peso DECIMAL(10,2),
    quantidade INT DEFAULT 0
);

-- Tabela de endereços
CREATE TABLE IF NOT EXISTS enderecos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('origem', 'destino') NOT NULL,
    nome VARCHAR(150) NOT NULL,
    cep VARCHAR(10) NOT NULL,
    rua VARCHAR(150) NOT NULL,
    numero VARCHAR(20) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    estado CHAR(2) NOT NULL,
    bairro VARCHAR(100),
    complemento VARCHAR(150)
);

-- Tabela de transportadoras
CREATE TABLE IF NOT EXISTS transportadoras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    cnpj VARCHAR(18) NOT NULL UNIQUE,
    telefone VARCHAR(20),
    email VARCHAR(150),
    endereco VARCHAR(255)
);

-- Tabela de fretes
CREATE TABLE IF NOT EXISTS fretes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transportadora_id INT NOT NULL,
    endereco_origem_id INT NOT NULL,
    endereco_destino_id INT NOT NULL,
    data_frete DATE NOT NULL,
    status ENUM(
        'Pendente',
        'Em andamento',
        'Concluído',
        'Cancelado'
    ) NOT NULL,

    FOREIGN KEY (transportadora_id)
        REFERENCES transportadoras(id),

    FOREIGN KEY (endereco_origem_id)
        REFERENCES enderecos(id),

    FOREIGN KEY (endereco_destino_id)
        REFERENCES enderecos(id)
);

-- Tabela de produtos vinculados aos fretes
CREATE TABLE IF NOT EXISTS itens_frete (
    id INT AUTO_INCREMENT PRIMARY KEY,
    frete_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,

    FOREIGN KEY (frete_id)
        REFERENCES fretes(id),

    FOREIGN KEY (produto_id)
        REFERENCES produtos(id)
);
