<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['excluir'])) {
        try {
            $pdo->prepare('DELETE FROM produtos WHERE id=?')->execute([$_POST['excluir']]);
            voltar('cadastro_produto.php', 'Produto excluído.');
        } catch (PDOException $ex) {
            voltar('cadastro_produto.php', 'Não foi possível excluir: o produto está em uma OS.');
        }
    }
    $d = [trim($_POST['nome']), (float)$_POST['preco']];
    if (!empty($_POST['id'])) {
        $pdo->prepare('UPDATE produtos SET nome=?, preco=? WHERE id=?')->execute([...$d, $_POST['id']]);
    } else {
        $pdo->prepare('INSERT INTO produtos (nome,preco) VALUES (?,?)')->execute($d);
    }
    voltar('cadastro_produto.php', 'Produto salvo com sucesso!');
}
$edit = null;
if (isset($_GET['editar'])) {
    $s = $pdo->prepare('SELECT * FROM produtos WHERE id=?'); $s->execute([$_GET['editar']]); $edit = $s->fetch();
}
$produtos = $pdo->query('SELECT * FROM produtos ORDER BY nome')->fetchAll();
$titulo = 'Produtos';
include 'header.php';
?>
<h1>Produtos</h1>
<div class="card">
  <h2><?= $edit ? 'Editar produto' : 'Novo produto' ?></h2>
  <form method="post" class="form">
    <input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
    <label>Nome do produto <input name="nome" required value="<?= e($edit['nome'] ?? '') ?>"></label>
    <label>Preço (R$) <input name="preco" type="number" step="0.01" min="0" required value="<?= e($edit['preco'] ?? '') ?>"></label>
    <div class="acoes"><button class="btn">💾 Salvar</button>
    <?php if ($edit): ?><a class="btn sec" href="cadastro_produto.php">Cancelar</a><?php endif; ?></div>
  </form>
</div>
<div class="card">
  <h2>Produtos cadastrados</h2>
  <input class="busca" placeholder="🔎 Buscar produto..." data-filtro="tb-produtos">
  <table id="tb-produtos">
    <tr><th>Produto</th><th>Preço</th><th></th></tr>
    <?php foreach ($produtos as $p): ?>
      <tr><td><?= e($p['nome']) ?></td><td><?= brl($p['preco']) ?></td>
      <td class="fim"><a href="?editar=<?= $p['id'] ?>">Editar</a>
        <form method="post" onsubmit="return confirm('Excluir este produto?')"><button class="link red" name="excluir" value="<?= $p['id'] ?>">Excluir</button></form></td></tr>
    <?php endforeach; if (!$produtos) echo '<tr><td colspan="3" class="vazio">Nenhum produto cadastrado.</td></tr>'; ?>
  </table>
</div>
<?php include 'footer.php'; ?>
