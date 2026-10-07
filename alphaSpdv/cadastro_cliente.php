<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['excluir'])) {
        try {
            $pdo->prepare('DELETE FROM clientes WHERE id=?')->execute([$_POST['excluir']]);
            voltar('cadastro_cliente.php', 'Cliente excluído.');
        } catch (PDOException $ex) {
            voltar('cadastro_cliente.php', 'Não foi possível excluir: o cliente possui ordens de serviço.');
        }
    }
    $d = [trim($_POST['nome']), trim($_POST['endereco']), trim($_POST['telefone'])];
    if (!empty($_POST['id'])) {
        $pdo->prepare('UPDATE clientes SET nome=?, endereco=?, telefone=? WHERE id=?')->execute([...$d, $_POST['id']]);
    } else {
        $pdo->prepare('INSERT INTO clientes (nome,endereco,telefone) VALUES (?,?,?)')->execute($d);
    }
    voltar('cadastro_cliente.php', 'Cliente salvo com sucesso!');
}
$edit = null;
if (isset($_GET['editar'])) {
    $s = $pdo->prepare('SELECT * FROM clientes WHERE id=?'); $s->execute([$_GET['editar']]); $edit = $s->fetch();
}
$clientes = $pdo->query('SELECT * FROM clientes ORDER BY nome')->fetchAll();
$titulo = 'Clientes';
include 'header.php';
?>
<h1>Clientes</h1>
<div class="card">
  <h2><?= $edit ? 'Editar cliente' : 'Novo cliente' ?></h2>
  <form method="post" class="form">
    <input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
    <label>Nome <input name="nome" required value="<?= e($edit['nome'] ?? '') ?>"></label>
    <label>Endereço <input name="endereco" required value="<?= e($edit['endereco'] ?? '') ?>"></label>
    <label>Telefone <input name="telefone" required placeholder="(11) 90000-0000" value="<?= e($edit['telefone'] ?? '') ?>"></label>
    <div class="acoes"><button class="btn">💾 Salvar</button>
    <?php if ($edit): ?><a class="btn sec" href="cadastro_cliente.php">Cancelar</a><?php endif; ?></div>
  </form>
</div>
<div class="card">
  <h2>Clientes cadastrados</h2>
  <input class="busca" placeholder="🔎 Buscar cliente..." data-filtro="tb-clientes">
  <table id="tb-clientes">
    <tr><th>Nome</th><th>Endereço</th><th>Telefone</th><th></th></tr>
    <?php foreach ($clientes as $c): ?>
      <tr><td><?= e($c['nome']) ?></td><td><?= e($c['endereco']) ?></td><td><?= e($c['telefone']) ?></td>
      <td class="fim"><a href="?editar=<?= $c['id'] ?>">Editar</a>
        <form method="post" onsubmit="return confirm('Excluir este cliente?')"><button class="link red" name="excluir" value="<?= $c['id'] ?>">Excluir</button></form></td></tr>
    <?php endforeach; if (!$clientes) echo '<tr><td colspan="4" class="vazio">Nenhum cliente cadastrado.</td></tr>'; ?>
  </table>
</div>
<?php include 'footer.php'; ?>
