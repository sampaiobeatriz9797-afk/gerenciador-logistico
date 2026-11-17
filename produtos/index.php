
<?php

require_once '../config/conexao.php';

// Inicializa as variáveis
$mensagem = '';
$tipoMensagem = '';

$nome = '';
$codigo = '';
$descricao = '';
$peso = '';
$quantidade = '';

$produtoEdicao = null;

// Identifica se existe um produto selecionado para edição
$idEdicao = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);

if ($idEdicao) {

    $sqlEdicao = "SELECT * FROM produtos WHERE id = :id";
    $consultaEdicao = $conexao->prepare($sqlEdicao);

    $consultaEdicao->execute([
        ':id' => $idEdicao
    ]);

    $produtoEdicao = $consultaEdicao->fetch(PDO::FETCH_ASSOC);

    if ($produtoEdicao) {

        $nome = $produtoEdicao['nome'];
        $codigo = $produtoEdicao['codigo'];
        $descricao = $produtoEdicao['descricao'];
        $peso = $produtoEdicao['peso'];
        $quantidade = $produtoEdicao['quantidade'];

    } else {

        $mensagem = "Produto não encontrado.";
        $tipoMensagem = "erro";

    }
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recebe os dados do formulário
    $idProduto = $_POST['id_produto'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $codigo = trim($_POST['codigo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $peso = $_POST['peso'] ?? '';
    $quantidade = $_POST['quantidade'] ?? '';

    // Valida os campos obrigatórios
    if ($nome === '' || $codigo === '') {

        $mensagem = "Preencha o nome e o código do produto.";
        $tipoMensagem = "erro";

    } elseif (
        ($peso !== '' && (!is_numeric($peso) || $peso < 0)) ||
        ($quantidade !== '' && !ctype_digit((string) $quantidade))
    ) {

        $mensagem = "Verifique os valores de peso e quantidade.";
        $tipoMensagem = "erro";

    } else {

        try {

            if ($idProduto !== '' && ctype_digit((string) $idProduto)) {

                // Atualiza o produto existente
                $sql = "UPDATE produtos SET
                        nome = :nome,
                        codigo = :codigo,
                        descricao = :descricao,
                        peso = :peso,
                        quantidade = :quantidade
                        WHERE id = :id";

                $consulta = $conexao->prepare($sql);

                $consulta->execute([
                    ':nome' => $nome,
                    ':codigo' => $codigo,
                    ':descricao' => $descricao,
                    ':peso' => $peso !== '' ? $peso : null,
                    ':quantidade' => $quantidade !== '' ? $quantidade : 0,
                    ':id' => $idProduto
                ]);

                $mensagem = "Produto atualizado com sucesso!";
                $tipoMensagem = "sucesso";

            } else {

                // Cadastra um novo produto
                $sql = "INSERT INTO produtos
                        (nome, codigo, descricao, peso, quantidade)
                        VALUES
                        (:nome, :codigo, :descricao, :peso, :quantidade)";

                $consulta = $conexao->prepare($sql);

                $consulta->execute([
                    ':nome' => $nome,
                    ':codigo' => $codigo,
                    ':descricao' => $descricao,
                    ':peso' => $peso !== '' ? $peso : null,
                    ':quantidade' => $quantidade !== '' ? $quantidade : 0
                ]);

                $mensagem = "Produto cadastrado com sucesso!";
                $tipoMensagem = "sucesso";
            }

            // Limpa os campos após salvar
            $nome = '';
            $codigo = '';
            $descricao = '';
            $peso = '';
            $quantidade = '';
            $produtoEdicao = null;

        } catch (PDOException $erro) {

            if ($erro->getCode() == '23000') {

                $mensagem = "Este código de produto já está cadastrado.";

            } else {

                $mensagem = "Não foi possível salvar o produto.";

            }

            $tipoMensagem = "erro";
        }
    }
}

// Busca os produtos cadastrados
$sqlProdutos = "
    SELECT id, nome, codigo, peso, quantidade
    FROM produtos
    ORDER BY id DESC
";

$consultaProdutos = $conexao->query($sqlProdutos);

$produtos = $consultaProdutos->fetchAll(PDO::FETCH_ASSOC);

// Inclui o cabeçalho
require_once '../includes/header.php';

?>

<!-- Cabeçalho da página -->
<header class="cabecalho">

    <h1>Cadastro de produtos</h1>

    <p>Cadastre e gerencie os produtos destinados às operações logísticas.</p>

</header>

<!-- Formulário de cadastro e edição -->
<section class="formulario-produto">

    <h2>
        <?= $produtoEdicao ? 'Editar produto' : 'Informações do produto' ?>
    </h2>

    <form action="" method="POST">

        <?php if ($produtoEdicao): ?>

            <input
                type="hidden"
                name="id_produto"
                value="<?= htmlspecialchars((string) $produtoEdicao['id']) ?>"
            >

        <?php endif; ?>

        <div class="linha-campos">

            <div class="campo">

                <label class="form-label" for="nome">
                    Nome do produto *
                </label>

                <input
                    class="form-control"
                    type="text"
                    id="nome"
                    name="nome"
                    value="<?= htmlspecialchars((string) $nome) ?>"
                    required
                >

            </div>

            <div class="campo">

                <label class="form-label" for="codigo">
                    Código identificador *
                </label>

                <input
                    class="form-control"
                    type="text"
                    id="codigo"
                    name="codigo"
                    value="<?= htmlspecialchars((string) $codigo) ?>"
                    required
                >

            </div>

        </div>

        <div class="campo">

            <label class="form-label" for="descricao">
                Descrição
            </label>

            <textarea
                class="form-control"
                id="descricao"
                name="descricao"
                rows="3"
            ><?= htmlspecialchars((string) $descricao) ?></textarea>

        </div>

        <div class="linha-campos">

            <div class="campo">

                <label class="form-label" for="peso">
                    Peso do produto (kg).
                </label>

                <input
                    class="form-control"
                    type="number"
                    id="peso"
                    name="peso"
                    step="0.01"
                    min="0"
                    value="<?= htmlspecialchars((string) $peso) ?>"
                >

            </div>

            <div class="campo">

                <label class="form-label" for="quantidade">
                    Quantidade
                </label>

                <input
                    class="form-control"
                    type="number"
                    id="quantidade"
                    name="quantidade"
                    min="0"
                    step="1"
                    value="<?= htmlspecialchars((string) $quantidade) ?>"
                >

            </div>

        </div>

        <div class="botoes-formulario">

            <?php if ($produtoEdicao): ?>

                <a href="index.php" class="btn btn-outline-secondary">
                    Cancelar edição
                </a>

                <button type="submit" class="btn btn-primary">
                    Salvar alterações
                </button>

            <?php else: ?>

                <button type="reset" class="btn btn-outline-secondary">
                    Limpar produto
                </button>

                <button type="submit" class="btn btn-primary">
                    Salvar produto
                </button>

            <?php endif; ?>

        </div>

        <?php if ($mensagem !== ''): ?>

            <div class="mensagem <?= htmlspecialchars($tipoMensagem) ?> alert <?= $tipoMensagem === 'sucesso' ? 'alert-success' : 'alert-danger' ?>" role="alert">

                <?= htmlspecialchars($mensagem) ?>

            </div>

        <?php endif; ?>

    </form>

</section>

<!-- Lista de produtos cadastrados -->
<section class="lista-produtos">

    <h2>Produtos cadastrados</h2>

    <div class="table-responsive">

        <table class="table table-hover align-middle">

            <thead class="table-light">

                <tr>
                    <th>Código</th>
                    <th>Produto</th>
                    <th>Peso (kg)</th>
                    <th>Quantidade</th>
                    <th>Ações</th>
                </tr>

            </thead>

            <tbody>

                <?php if (count($produtos) > 0): ?>

                    <?php foreach ($produtos as $produto): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($produto['codigo']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($produto['nome']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars((string) $produto['peso']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars((string) $produto['quantidade']) ?>
                            </td>

                            <td>

                                <a
                                    href="index.php?editar=<?= (int) $produto['id'] ?>"
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    Editar
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="5" class="text-center">
                            Nenhum produto cadastrado.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</section>

<?php
require_once '../includes/footer.php';
?>


