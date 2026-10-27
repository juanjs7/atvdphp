<?php
require_once "verifica_login.php";   // bloqueia quem não fez login
require_once "conexao.php";

$categorias = ["Café", "Doce", "Salgado", "Bebida Gelada"];
$msg  = $_GET['msg'] ?? '';
$erro = "";

// ---------- C - CREATE (cadastrar) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome      = trim($_POST['nome_item'] ?? '');
    $categoria = $_POST['categoria'] ?? '';
    $descricao = trim($_POST['descricao'] ?? '');
    $preco     = str_replace(',', '.', $_POST['preco'] ?? '');

    if ($nome === '' || !in_array($categoria, $categorias) || !is_numeric($preco) || $preco < 0) {
        $erro = "Preencha todos os campos corretamente.";
    } else {
        $preco = (float) $preco;
        $stmt = mysqli_prepare($conn,
            "INSERT INTO cardapio (nome_item, categoria, descricao, preco) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssd", $nome, $categoria, $descricao, $preco);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            // Redireciona para evitar cadastro duplicado ao recarregar a página (F5)
            header("Location: painel.php?msg=cadastrado");
            exit;
        }
        $erro = "Erro ao cadastrar o item.";
        mysqli_stmt_close($stmt);
    }
}

// ---------- R - READ (listar) ----------
$stmt = mysqli_prepare($conn, "SELECT id, nome_item, categoria, descricao, preco FROM cardapio ORDER BY categoria, nome_item");
mysqli_stmt_execute($stmt);
$itens = mysqli_stmt_get_result($stmt);

$mensagens = [
    'cadastrado' => 'Item cadastrado com sucesso!',
    'editado'    => 'Item atualizado com sucesso!',
    'excluido'   => 'Item excluído com sucesso!',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Cardápio</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="topo">
        <h1>☕ Gestão do Cardápio</h1>
        <div>
            Olá, <strong><?= htmlspecialchars($_SESSION['usuario_nome']) ?></strong>
            <a class="btn sair" href="logout.php">Sair</a>
        </div>
    </header>

    <main class="container">
        <?php if (isset($mensagens[$msg])): ?>
            <div class="alerta sucesso"><?= $mensagens[$msg] ?></div>
        <?php endif; ?>
        <?php if ($erro): ?>
            <div class="alerta erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <section class="card">
            <h2>Novo item</h2>
            <form method="POST" action="painel.php">
                <label for="nome_item">Nome do Item</label>
                <input type="text" id="nome_item" name="nome_item" maxlength="100" required>

                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat ?>"><?= $cat ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" rows="3"></textarea>

                <label for="preco">Preço (R$)</label>
                <input type="number" id="preco" name="preco" step="0.01" min="0" required>

                <button type="submit">Cadastrar</button>
            </form>
        </section>

        <section class="card">
            <h2>Itens cadastrados</h2>
            <?php if (mysqli_num_rows($itens) === 0): ?>
                <p>Nenhum item cadastrado ainda.</p>
            <?php else: ?>
            <div class="tabela-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Nome</th><th>Categoria</th><th>Descrição</th><th>Preço</th><th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($item = mysqli_fetch_assoc($itens)): ?>
                    <tr>
                        <td><?= $item['id'] ?></td>
                        <td><?= htmlspecialchars($item['nome_item']) ?></td>
                        <td><?= htmlspecialchars($item['categoria']) ?></td>
                        <td><?= htmlspecialchars($item['descricao']) ?></td>
                        <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                        <td class="acoes">
                            <a class="btn editar" href="editar_item.php?id=<?= $item['id'] ?>">Editar</a>
                            <a class="btn excluir" href="excluir_item.php?id=<?= $item['id'] ?>"
                               onclick="return confirm('Excluir este item?');">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
<?php mysqli_stmt_close($stmt); ?>
