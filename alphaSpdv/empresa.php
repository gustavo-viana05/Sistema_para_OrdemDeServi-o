<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->prepare('UPDATE empresa SET nome=?, telefone=?, email=?, cnpj=?, endereco=?, pix=?, banco=?, titular=? WHERE id=1')->execute([
        trim($_POST['nome']), trim($_POST['telefone']), trim($_POST['email']), trim($_POST['cnpj']), trim($_POST['endereco']),
        trim($_POST['pix']), trim($_POST['banco']), trim($_POST['titular'])
    ]);
    voltar('empresa.php', 'Dados da empresa salvos!');
}
$titulo = 'Empresa';
include 'header.php';
?>
<h1>Empresa</h1>
<div class="card">
  <p class="mut">Estes dados aparecem em todas as notas (PDF): os dados da empresa no topo e a forma de pagamento logo após os valores.</p>
  <form method="post" class="form">
    <label class="full">Nome da empresa <input name="nome" required value="<?= e($empresa['nome']) ?>"></label>
    <label>Telefone <input name="telefone" placeholder="(11) 90000-0000" value="<?= e($empresa['telefone']) ?>"></label>
    <label>E-mail <input name="email" type="email" placeholder="contato@empresa.com.br" value="<?= e($empresa['email']) ?>"></label>
    <label>CNPJ (opcional) <input name="cnpj" value="<?= e($empresa['cnpj']) ?>"></label>
    <label>Endereço (opcional) <input name="endereco" value="<?= e($empresa['endereco']) ?>"></label>
    <h2 class="full" style="margin:8px 0 0">Forma de pagamento</h2>
    <label class="full">Chave Pix <input name="pix" placeholder="CPF, CNPJ, e-mail, telefone ou chave aleatória" value="<?= e($empresa['pix']) ?>"></label>
    <label>Banco <input name="banco" placeholder="Nome do banco" value="<?= e($empresa['banco']) ?>"></label>
    <label>Titular da conta <input name="titular" value="<?= e($empresa['titular']) ?>"></label>
    <div class="acoes full"><button class="btn">💾 Salvar</button></div>
  </form>
</div>
<div class="card">
  <h2>Logo</h2>
  <img src="assets/logo.png" alt="Logo" style="max-width:260px;border-radius:12px">
  <p class="mut">Para trocar a logo, substitua o arquivo <code>assets/logo.png</code> (e <code>assets/logo_icon.png</code> para o menu).</p>
</div>
<?php include 'footer.php'; ?>
