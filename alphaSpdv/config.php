<?php
// ====== BANCO DE DADOS (padrão do XAMPP) ======
$db = ['host' => 'localhost', 'nome' => 'sistema_os', 'user' => 'root', 'pass' => ''];

try {
    $pdo = new PDO("mysql:host={$db['host']};dbname={$db['nome']};charset=utf8mb4", $db['user'], $db['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
} catch (PDOException $e) {
    die('Erro ao conectar no banco. Você importou o install.sql no phpMyAdmin? <br>' . htmlspecialchars($e->getMessage()));
}

// Tabela da empresa (criada automaticamente se ainda não existir)
$pdo->exec("CREATE TABLE IF NOT EXISTS empresa (
  id INT PRIMARY KEY, nome VARCHAR(120) NOT NULL, telefone VARCHAR(20), email VARCHAR(120),
  cnpj VARCHAR(20), endereco VARCHAR(200))");
$pdo->exec("INSERT IGNORE INTO empresa (id, nome) VALUES (1, 'Alpha Solutions')");
if (!$pdo->query("SHOW COLUMNS FROM ordens_servico LIKE 'tecnico'")->fetch()) {
    $pdo->exec("ALTER TABLE ordens_servico ADD COLUMN tecnico VARCHAR(120) NULL AFTER status");
}
// Número inicial das ordens de serviço (a próxima OS criada terá este número, ou o seguinte ao maior já existente)
$os_inicial = 113;
$prox = $pdo->prepare("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ordens_servico'");
$prox->execute();
if ((int)$prox->fetchColumn() < $os_inicial) {
    $pdo->exec("ALTER TABLE ordens_servico AUTO_INCREMENT = $os_inicial");
}
foreach (['pix' => 'VARCHAR(120)', 'banco' => 'VARCHAR(80)', 'titular' => 'VARCHAR(120)'] as $col => $tipo) {
    if (!$pdo->query("SHOW COLUMNS FROM empresa LIKE '$col'")->fetch()) {
        $pdo->exec("ALTER TABLE empresa ADD COLUMN $col $tipo NULL");
    }
}
$empresa = $pdo->query('SELECT * FROM empresa WHERE id=1')->fetch();

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function brl($v) { return 'R$ ' . number_format((float)$v, 2, ',', '.'); }
function voltar($arquivo, $msg) { header("Location: $arquivo" . (strpos($arquivo, '?') ? '&' : '?') . 'msg=' . urlencode($msg)); exit; }
