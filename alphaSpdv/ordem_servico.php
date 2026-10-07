<?php
require 'config.php';
$precos = $pdo->query('SELECT id,preco FROM produtos')->fetchAll(PDO::FETCH_KEY_PAIR);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $c = [$_POST['cliente_id'], $_POST['tipo_equipamento'], trim($_POST['defeito']), trim($_POST['laudo']), $_POST['status'], (float)$_POST['mao_de_obra'], trim($_POST['tecnico'])];
    $pdo->beginTransaction();
    if ($id) {
        $pdo->prepare('UPDATE ordens_servico SET cliente_id=?, tipo_equipamento=?, defeito=?, laudo=?, status=?, mao_de_obra=?, tecnico=? WHERE id=?')->execute([...$c, $id]);
        $pdo->prepare('DELETE FROM os_itens WHERE os_id=?')->execute([$id]);
    } else {
        $pdo->prepare('INSERT INTO ordens_servico (cliente_id,tipo_equipamento,defeito,laudo,status,mao_de_obra,tecnico) VALUES (?,?,?,?,?,?,?)')->execute($c);
        $id = $pdo->lastInsertId();
    }
    $ins = $pdo->prepare('INSERT INTO os_itens (os_id,produto_id,quantidade,preco_unit) VALUES (?,?,?,?)');
    foreach (($_POST['qtd'] ?? []) as $pid => $q) {
        if ((int)$q > 0 && isset($precos[$pid])) {
            $v = $_POST['preco'][$pid] ?? '';
            $ins->execute([$id, $pid, (int)$q, $v !== '' ? (float)$v : $precos[$pid]]);
        }
    }
    $pdo->commit();
    voltar("nota_os.php?id=$id", 'OS nº ' . str_pad($id, 6, '0', STR_PAD_LEFT) . ' salva com sucesso!');
}

$os = null; $qtds = []; $pus = [];
if (isset($_GET['id'])) {
    $s = $pdo->prepare('SELECT * FROM ordens_servico WHERE id=?'); $s->execute([$_GET['id']]); $os = $s->fetch();
    $s = $pdo->prepare('SELECT produto_id,quantidade FROM os_itens WHERE os_id=?'); $s->execute([$_GET['id']]); $qtds = $s->fetchAll(PDO::FETCH_KEY_PAIR);
    $s = $pdo->prepare('SELECT produto_id,preco_unit FROM os_itens WHERE os_id=?'); $s->execute([$_GET['id']]); $pus = $s->fetchAll(PDO::FETCH_KEY_PAIR);
}
$clientes = $pdo->query('SELECT id,nome,telefone FROM clientes ORDER BY nome')->fetchAll();
$produtos = $pdo->query('SELECT * FROM produtos ORDER BY nome')->fetchAll();
$titulo = $os ? 'Editar OS' : 'Nova OS';
include 'header.php';
?>
<h1><?= $os ? 'Editar OS #' . str_pad($os['id'], 6, '0', STR_PAD_LEFT) : 'Nova Ordem de Serviço' ?></h1>
<?php if (!$clientes): ?>
  <div class="card">Cadastre um cliente antes de criar uma OS. <a href="cadastro_cliente.php">Ir para clientes →</a></div>
<?php else: ?>
<form method="post" class="card form">
  <input type="hidden" name="id" value="<?= e($os['id'] ?? '') ?>">
  <label>Cliente
    <select name="cliente_id" required>
      <option value="">Selecione o cliente...</option>
      <?php foreach ($clientes as $c): ?>
        <option value="<?= $c['id'] ?>" <?= ($os['cliente_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?> — <?= e($c['telefone']) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Tipo de equipamento
    <select name="tipo_equipamento" required>
      <?php foreach (['Desktop', 'Video Game', 'Notebook'] as $t): ?>
        <option <?= ($os['tipo_equipamento'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Status
    <select name="status">
      <?php foreach (['Aberta', 'Em andamento', 'Finalizada'] as $t): ?>
        <option <?= ($os['status'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Técnico que atendeu <input name="tecnico" placeholder="Nome do técnico" value="<?= e($os['tecnico'] ?? '') ?>"></label>
  <label class="full">Defeito relatado <textarea name="defeito" rows="3" required><?= e($os['defeito'] ?? '') ?></textarea></label>
  <label class="full">Laudo técnico / serviço realizado <textarea name="laudo" rows="4"><?= e($os['laudo'] ?? '') ?></textarea></label>

  <div class="full">
    <h2>Produtos utilizados (opcional)</h2>
    <table>
      <tr><th>Produto</th><th>Preço cadastrado</th><th style="width:150px">Valor nesta OS (R$)</th><th style="width:90px">Qtd</th></tr>
      <?php foreach ($produtos as $p): ?>
        <tr><td><?= e($p['nome']) ?></td><td><?= brl($p['preco']) ?></td>
        <td><input class="pv" type="number" step="0.01" min="0" name="preco[<?= $p['id'] ?>]" data-base="<?= $p['preco'] ?>" value="<?= e($pus[$p['id']] ?? $p['preco']) ?>"></td>
        <td><input class="qtd" type="number" min="0" value="<?= (int)($qtds[$p['id']] ?? 0) ?>" name="qtd[<?= $p['id'] ?>]"></td></tr>
      <?php endforeach; if (!$produtos) echo '<tr><td colspan="4" class="vazio">Nenhum produto cadastrado.</td></tr>'; ?>
    </table>
  </div>


  <div class="acoes full"><button class="btn">💾 Salvar OS</button><a class="btn sec" href="listar_os.php">Cancelar</a></div>
</form>
<?php endif; ?>
<?php include 'footer.php'; ?>
