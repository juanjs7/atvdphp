<?php
require_once "verifica_login.php";
require_once "conexao.php";

$categorias = ["Café", "Doce", "Salgado", "Bebida Gelada"];
$erro = "";

// O ID vem pela URL (?id=X) ao abrir, e por campo oculto ao salvar
$id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: painel.php");
    exit;
}

// ---------- U - UPDATE (salvar alterações) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome      = trim($_POST['nome_item'] ?? '');
    $categoria = $_POST['categoria'] ?? '';
    $descricao = trim($_POST['descricao'] ?? '');
    $preco     = str_replace(',', '.', $_POST['preco'] ?? '');

    if ($nome === '' || !in_array($categoria, $categorias) || !is_numeric($preco) || $preco < 0) {
        $erro = "Preencha todos os campos corretamente.";
    } else {
        $preco = (float) $preco;
        // UPDATE com WHERE id = ? -> altera o item existente, sem duplicar
        $stmt = mysqli_prepare($conn,
            "UPDATE cardapio SET nome_item = ?, categoria = ?, descricao = ?, preco = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "sssdi", $nome, $categoria, $descricao, $preco, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: painel.php?msg=editado");
        exit;
    }
}

// Busca os dados atuais para preencher o formulário
$stmt = mysqli_prepare($conn, "SELECT id, nome_item, categoria, descricao, preco FROM cardapio WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$item = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$item) {
    header("Location: painel.php");
    exit;
}

// Se houve erro na validação, mantém o que o usuário digitou
if ($erro) {
    $item['nome_item'] = $_POST['nome_item'];
    $item['categoria'] = $_POST['categoria'];
    $item['descricao'] = $_POST['descricao'];
    $item['preco']     = $_POST['preco'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Item - Cardápio</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="topo">
        <h1>☕ Editar item</h1>
        <a class="btn sair" href="logout.php">Sair</a>
    </header>

    <main class="container">
        <?php if ($erro): ?>
            <div class="alerta erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <section class="card">
            <form method="POST" action="editar_item.php">
                <input type="hidden" name="id" value="<?= $item['id'] ?>">

                <label for="nome_item">Nome do Item</label>
                <input type="text" id="nome_item" name="nome_item" maxlength="100"
                       value="<?= htmlspecialchars($item['nome_item']) ?>" required>

                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria" required>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat ?>" <?= $item['categoria'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" rows="3"><?= htmlspecialchars($item['descricao']) ?></textarea>

                <label for="preco">Preço (R$)</label>
                <input type="number" id="preco" name="preco" step="0.01" min="0"
                       value="<?= htmlspecialchars($item['preco']) ?>" required>

                <div class="linha-botoes">
                    <button type="submit">Salvar alterações</button>
                    <a class="btn voltar" href="painel.php">Cancelar</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
