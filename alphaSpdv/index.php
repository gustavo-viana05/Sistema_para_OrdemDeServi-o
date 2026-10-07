<?php
require 'config.php';
$tot = [
    'Clientes'  => $pdo->query('SELECT COUNT(*) FROM clientes')->fetchColumn(),
    'Produtos'  => $pdo->query('SELECT COUNT(*) FROM produtos')->fetchColumn(),
    'OS abertas' => $pdo->query("SELECT COUNT(*) FROM ordens_servico WHERE status<>'Finalizada'")->fetchColumn(),
    'OS finalizadas' => $pdo->query("SELECT COUNT(*) FROM ordens_servico WHERE status='Finalizada'")->fetchColumn(),
];
$ultimas = $pdo->query('SELECT o.id,o.tipo_equipamento,o.status,c.nome FROM ordens_servico o JOIN clientes c ON c.id=o.cliente_id ORDER BY o.id DESC LIMIT 5')->fetchAll();
$titulo = 'Início';
include 'header.php';
?>
<h1>Painel</h1>
<div class="cards">
  <?php foreach ($tot as $l => $v): ?><div class="card stat"><span><?= $v ?></span><small><?= $l ?></small></div><?php endforeach; ?>
</div>
<div class="acoes">
  <a class="btn" href="ordem_servico.php">➕ Nova Ordem de Serviço</a>
  <a class="btn sec" href="cadastro_cliente.php">👤 Cadastrar cliente</a>
  <a class="btn sec" href="cadastro_produto.php">📦 Cadastrar produto</a>
</div>
<div class="card">
  <h2>Últimas ordens de serviço</h2>
  <table>
    <tr><th>Nº</th><th>Cliente</th><th>Equipamento</th><th>Status</th><th></th></tr>
    <?php foreach ($ultimas as $o): ?>
      <tr><td>#<?= str_pad($o['id'], 6, '0', STR_PAD_LEFT) ?></td><td><?= e($o['nome']) ?></td><td><?= e($o['tipo_equipamento']) ?></td>
      <td><span class="tag s<?= strlen($o['status']) ?>"><?= e($o['status']) ?></span></td><td><a href="nota_os.php?id=<?= $o['id'] ?>">Ver nota</a></td></tr>
    <?php endforeach; if (!$ultimas) echo '<tr><td colspan="5" class="vazio">Nenhuma OS ainda.</td></tr>'; ?>
  </table>
</div>
<?php include 'footer.php'; ?>
