<?php
require 'config.php';
$s = $pdo->prepare('SELECT o.*, c.nome, c.endereco, c.telefone FROM ordens_servico o JOIN clientes c ON c.id=o.cliente_id WHERE o.id=?');
$s->execute([$_GET['id'] ?? 0]);
$os = $s->fetch();
if (!$os) die('OS não encontrada.');
$s = $pdo->prepare('SELECT i.*, p.nome FROM os_itens i JOIN produtos p ON p.id=i.produto_id WHERE i.os_id=?');
$s->execute([$os['id']]);
$itens = $s->fetchAll();
$total = $os['mao_de_obra'];
foreach ($itens as $i) $total += $i['quantidade'] * $i['preco_unit'];
$num = str_pad($os['id'], 6, '0', STR_PAD_LEFT);
$titulo = "Nota OS $num";
include 'header.php';
?>
<div class="acoes no-print">
  <button class="btn" onclick="window.print()">📄 Gerar PDF</button>
  <a class="btn sec" href="ordem_servico.php?id=<?= $os['id'] ?>">Editar OS</a>
  <a class="btn sec" href="listar_os.php">Voltar</a>
</div>
<div class="nota">
  <div class="nota-topo">
    <img class="nota-logo" src="assets/logo.png" alt="Logo">
    <div class="nota-emp">
      <h1><?= e($empresa['nome']) ?></h1>
      <p>
        <?php if ($empresa['telefone']): ?>📞 <?= e($empresa['telefone']) ?><br><?php endif; ?>
        <?php if ($empresa['email']): ?>✉️ <?= e($empresa['email']) ?><br><?php endif; ?>
        <?php if ($empresa['cnpj']): ?>CNPJ: <?= e($empresa['cnpj']) ?><br><?php endif; ?>
        <?= e($empresa['endereco']) ?>
      </p>
    </div>
    <div class="nota-num">ORDEM DE SERVIÇO<strong>Nº <?= $num ?></strong><small><?= date('d/m/Y', strtotime($os['criado_em'])) ?></small></div>
  </div>
  <h3>Dados do cliente</h3>
  <p><b>Nome:</b> <?= e($os['nome']) ?><br><b>Endereço:</b> <?= e($os['endereco']) ?><br><b>Telefone:</b> <?= e($os['telefone']) ?></p>
  <h3>Equipamento</h3>
  <p><b>Tipo:</b> <?= e($os['tipo_equipamento']) ?> &nbsp; <b>Status:</b> <?= e($os['status']) ?><?php if ($os['tecnico']): ?> &nbsp; <b>Técnico:</b> <?= e($os['tecnico']) ?><?php endif; ?></p>
  <p><b>Defeito relatado:</b><br><?= nl2br(e($os['defeito'])) ?></p>
  <h3>Serviço realizado (laudo)</h3>
  <p><?= $os['laudo'] ? nl2br(e($os['laudo'])) : '—' ?></p>
  <h3>Valores</h3>
  <table>
    <tr><th>Descrição</th><th>Qtd</th><th>Unitário</th><th>Subtotal</th></tr>
    <?php foreach ($itens as $i): ?>
      <tr><td><?= e($i['nome']) ?></td><td><?= $i['quantidade'] ?></td><td><?= brl($i['preco_unit']) ?></td><td><?= brl($i['quantidade'] * $i['preco_unit']) ?></td></tr>
    <?php endforeach; ?>
    
    <tr class="tot"><td colspan="3">TOTAL</td><td><?= brl($total) ?></td></tr>
  </table>
  <?php if ($empresa['pix'] || $empresa['banco'] || $empresa['titular']): ?>
  <div class="pagto">
    <b>FORMA DE PAGAMENTO</b>
    <?php if ($empresa['pix']): ?><div class="pix">Pix: <?= e($empresa['pix']) ?></div><?php endif; ?>
    <?php if ($empresa['banco']): ?><div>Banco: <?= e($empresa['banco']) ?></div><?php endif; ?>
    <?php if ($empresa['titular']): ?><div>Titular: <?= e($empresa['titular']) ?></div><?php endif; ?>
  </div>
  <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
