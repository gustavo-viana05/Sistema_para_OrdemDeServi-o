<?php $pag = basename($_SERVER['PHP_SELF']); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($titulo ?? 'Sistema de OS') ?> · <?= e($empresa['nome']) ?></title>
<link rel="icon" href="assets/logo_icon.png">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<nav class="nav">
  <a class="brand" href="index.php"><img src="assets/logo_icon.png" alt=""><span><?= e($empresa['nome']) ?></span></a>
  <div class="links">
    <?php foreach (['index.php' => 'Início', 'cadastro_cliente.php' => 'Clientes', 'cadastro_produto.php' => 'Produtos',
                    'ordem_servico.php' => 'Nova OS', 'listar_os.php' => 'Ordens de Serviço', 'empresa.php' => 'Empresa'] as $f => $l): ?>
      <a href="<?= $f ?>" class="<?= $pag === $f ? 'on' : '' ?>"><?= $l ?></a>
    <?php endforeach; ?>
  </div>
</nav>
<main class="wrap">
<?php if (!empty($_GET['msg'])): ?><div class="toast"><?= e($_GET['msg']) ?></div><?php endif; ?>
