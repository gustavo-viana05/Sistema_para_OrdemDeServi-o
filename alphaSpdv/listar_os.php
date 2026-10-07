<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir'])) {
    $pdo->prepare('DELETE FROM ordens_servico WHERE id=?')->execute([$_POST['excluir']]);
    voltar('listar_os.php', 'OS excluída.');
}
$lista = $pdo->query('SELECT o.*, c.nome FROM ordens_servico o JOIN clientes c ON c.id=o.cliente_id ORDER BY o.id DESC')->fetchAll();
$titulo = 'Ordens de Serviço';
include 'header.php';
?>
<h1>Ordens de Serviço</h1>
<div class="card">
  <input class="busca" placeholder="🔎 Buscar por número, cliente, equipamento ou status..." data-filtro="tb-os">
  <table id="tb-os">
    <tr><th>Nº</th><th>Data</th><th>Cliente</th><th>Equipamento</th><th>Status</th><th></th></tr>
    <?php foreach ($lista as $o): ?>
      <tr>
        <td>#<?= str_pad($o['id'], 6, '0', STR_PAD_LEFT) ?></td>
        <td><?= date('d/m/Y', strtotime($o['criado_em'])) ?></td>
        <td><?= e($o['nome']) ?></td><td><?= e($o['tipo_equipamento']) ?></td>
        <td><span class="tag s<?= strlen($o['status']) ?>"><?= e($o['status']) ?></span></td>
        <td class="fim">
          <a href="nota_os.php?id=<?= $o['id'] ?>">📄 Nota</a>
          <a href="ordem_servico.php?id=<?= $o['id'] ?>">Editar</a>
          <form method="post" onsubmit="return confirm('Excluir esta OS?')"><button class="link red" name="excluir" value="<?= $o['id'] ?>">Excluir</button></form>
        </td>
      </tr>
    <?php endforeach; if (!$lista) echo '<tr><td colspan="6" class="vazio">Nenhuma OS cadastrada.</td></tr>'; ?>
  </table>
</div>
<?php include 'footer.php'; ?>
