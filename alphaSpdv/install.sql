CREATE DATABASE IF NOT EXISTS sistema_os CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_os;

CREATE TABLE IF NOT EXISTS clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  endereco VARCHAR(200) NOT NULL,
  telefone VARCHAR(20) NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  preco DECIMAL(10,2) NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS ordens_servico (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT NOT NULL,
  tipo_equipamento ENUM('Desktop','Video Game','Notebook') NOT NULL,
  defeito TEXT NOT NULL,
  laudo TEXT,
  status ENUM('Aberta','Em andamento','Finalizada') NOT NULL DEFAULT 'Aberta',
  tecnico VARCHAR(120),
  mao_de_obra DECIMAL(10,2) NOT NULL DEFAULT 0,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id)
) AUTO_INCREMENT=113;

CREATE TABLE IF NOT EXISTS os_itens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  os_id INT NOT NULL,
  produto_id INT NOT NULL,
  quantidade INT NOT NULL,
  preco_unit DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (os_id) REFERENCES ordens_servico(id) ON DELETE CASCADE,
  FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

CREATE TABLE IF NOT EXISTS empresa (
  id INT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  telefone VARCHAR(20),
  email VARCHAR(120),
  cnpj VARCHAR(20),
  endereco VARCHAR(200),
  pix VARCHAR(120),
  banco VARCHAR(80),
  titular VARCHAR(120)
);
INSERT IGNORE INTO empresa (id, nome) VALUES (1, 'Alpha Solutions');
