
<?php

require_once '../config/conexao.php';

$mensagem = '';
$tipoMensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $produtosSelecionados = $_POST['produtos'] ?? [];
    $quantidades = $_POST['quantidades'] ?? [];

    $origem = $_POST['origem'] ?? '';
    $destino = $_POST['destino'] ?? '';
    $transportadora = $_POST['transportadora'] ?? '';
    $dataFrete = $_POST['data_frete'] ?? '';
    $status = $_POST['status'] ?? '';

    $statusPermitidos = [
        'Pendente',
        'Em andamento',
        'Concluído',
        'Cancelado'
    ];

    // Verifica se os produtos e quantidades foram enviados corretamente
    $dadosProdutosValidos =
        is_array($produtosSelecionados) &&
        is_array($quantidades) &&
        count($produtosSelecionados) > 0 &&
        count($produtosSelecionados) === count($quantidades);

    if ($dadosProdutosValidos) {

        foreach ($produtosSelecionados as $indice => $produtoId) {

            if (
                !ctype_digit((string) $produtoId) ||
                !isset($quantidades[$indice]) ||
                !ctype_digit((string) $quantidades[$indice]) ||
                (int) $quantidades[$indice] < 1
            ) {

                $dadosProdutosValidos = false;
                break;

            }
        }
    }

    if (
        !$dadosProdutosValidos ||
        !ctype_digit((string) $origem) ||
        !ctype_digit((string) $destino) ||
        !ctype_digit((string) $transportadora) ||
        $dataFrete === '' ||
        !in_array($status, $statusPermitidos, true)
    ) {

        $mensagem = 'Preencha corretamente todos os campos do frete e dos produtos.';
        $tipoMensagem = 'erro';

    } else {

        try {

            $conexao->beginTransaction();

            // Registra o frete
            $sqlFrete = "INSERT INTO fretes
                (transportadora_id, endereco_origem_id,
                endereco_destino_id, data_frete, status)
                VALUES
                (:transportadora, :origem, :destino, :data_frete, :status)";

            $consultaFrete = $conexao->prepare($sqlFrete);

            $consultaFrete->execute([
                ':transportadora' => $transportadora,
                ':origem' => $origem,
                ':destino' => $destino,
                ':data_frete' => $dataFrete,
                ':status' => $status
            ]);

            // Recupera o ID do frete criado
            $freteId = $conexao->lastInsertId();

            // Prepara o comando para vincular os produtos ao frete
            $sqlItem = "INSERT INTO itens_frete
                (frete_id, produto_id, quantidade)
                VALUES
                (:frete_id, :produto_id, :quantidade)";

            $consultaItem = $conexao->prepare($sqlItem);

            // Percorre todos os produtos selecionados
            foreach ($produtosSelecionados as $indice => $produtoId) {

                $consultaItem->execute([
                    ':frete_id' => $freteId,
                    ':produto_id' => $produtoId,
                    ':quantidade' => $quantidades[$indice]
                ]);

            }

            // Confirma todas as gravações
            $conexao->commit();

            $mensagem = 'Frete cadastrado com sucesso!';
            $tipoMensagem = 'sucesso';

        } catch (PDOException $erro) {

            if ($conexao->inTransaction()) {
                $conexao->rollBack();
            }

            $mensagem = 'Não foi possível cadastrar o frete.';
            $tipoMensagem = 'erro';

        }
    }
}

// Busca os produtos cadastrados
$sqlProdutos = "SELECT id, nome FROM produtos ORDER BY nome";
$consultaProdutos = $conexao->query($sqlProdutos);
$produtos = $consultaProdutos->fetchAll(PDO::FETCH_ASSOC);

// Busca os endereços de origem
$sqlOrigens = "
    SELECT id, nome
    FROM enderecos
    WHERE tipo = 'origem'
    ORDER BY nome
";

$consultaOrigens = $conexao->query($sqlOrigens);
$origens = $consultaOrigens->fetchAll(PDO::FETCH_ASSOC);

// Busca os endereços de destino
$sqlDestinos = "
    SELECT id, nome
    FROM enderecos
    WHERE tipo = 'destino'
    ORDER BY nome
";

$consultaDestinos = $conexao->query($sqlDestinos);
$destinos = $consultaDestinos->fetchAll(PDO::FETCH_ASSOC);

// Busca as transportadoras
$sqlTransportadoras = "
    SELECT id, nome
    FROM transportadoras
    ORDER BY nome
";

$consultaTransportadoras = $conexao->query($sqlTransportadoras);
$transportadoras = $consultaTransportadoras->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';

?>

<header class="cabecalho">

    <h1>Cadastro de Fretes</h1>

    <p>Registre e acompanhe os fretes, vinculando produtos, endereços e transportadoras.</p>

</header>

<section class="formulario-produto">

    <h2>Informações do frete</h2>

    <form action="" method="POST">

        <!-- Produtos vinculados ao frete -->
        <div class="campo">

            <label class="form-label">
                Produtos do frete: *
            </label>

            <div id="lista-produtos-frete">

                <div class="linha-campos linha-produto-frete">

                    <div class="campo">

                        <label class="form-label">
                            Produto *
                        </label>

                        <select
                            class="form-select"
                            name="produtos[]"
                            required
                        >

                            <option value="">Selecione o produto</option>

                            <?php foreach ($produtos as $produto): ?>

                                <option value="<?= (int) $produto['id'] ?>">
                                    <?= htmlspecialchars($produto['nome']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="campo">

                        <label class="form-label">
                            Quantidade *
                        </label>

                        <input
                            class="form-control"
                            type="number"
                            name="quantidades[]"
                            min="1"
                            step="1"
                            value="1"
                            required
                        >

                    </div>

                    <button
                        type="button"
                        class="remover-produto btn btn-outline-danger btn-sm"
                        onclick="removerProduto(this)"
                        disabled
                    >
                        Remover
                    </button>

                </div>

            </div>

            <button
                type="button"
                id="adicionar-produto"
                class="btn btn-outline-primary mt-2"
            >
                + Adicionar produto
            </button>

        </div>

        <!-- Endereços -->
        <div class="linha-campos">

            <div class="campo">

                <label class="form-label" for="origem">
                    Endereço de origem: *
                </label>

                <select
                    class="form-select"
                    id="origem"
                    name="origem"
                    required
                >

                    <option value="">Selecione o endereço de origem</option>

                    <?php foreach ($origens as $origem): ?>

                        <option value="<?= (int) $origem['id'] ?>">
                            <?= htmlspecialchars($origem['nome']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="campo">

                <label class="form-label" for="destino">
                    Endereço de destino: *
                </label>

                <select
                    class="form-select"
                    id="destino"
                    name="destino"
                    required
                >

                    <option value="">Selecione o endereço de destino</option>

                    <?php foreach ($destinos as $destino): ?>

                        <option value="<?= (int) $destino['id'] ?>">
                            <?= htmlspecialchars($destino['nome']) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

        </div>

        <!-- Transportadora -->
        <div class="campo">

            <label class="form-label" for="transportadora">
                Transportadora: *
            </label>

            <select
                class="form-select"
                id="transportadora"
                name="transportadora"
                required
            >

                <option value="">Selecione a transportadora</option>

                <?php foreach ($transportadoras as $transportadora): ?>

                    <option value="<?= (int) $transportadora['id'] ?>">
                        <?= htmlspecialchars($transportadora['nome']) ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <!-- Data e status -->
        <div class="linha-campos">

            <div class="campo">

                <label class="form-label" for="data_frete">
                    Data do frete: *
                </label>

                <input
                    class="form-control"
                    type="date"
                    id="data_frete"
                    name="data_frete"
                    required
                >

            </div>

            <div class="campo">

                <label class="form-label" for="status">
                    Status do frete: *
                </label>

                <select
                    class="form-select"
                    id="status"
                    name="status"
                    required
                >

                    <option value="">Selecione o status</option>
                    <option value="Pendente">Pendente</option>
                    <option value="Em andamento">Em andamento</option>
                    <option value="Concluído">Concluído</option>
                    <option value="Cancelado">Cancelado</option>

                </select>

            </div>

        </div>

        <div class="botoes-formulario">

            <button type="reset" class="btn btn-outline-secondary">
                Limpar frete
            </button>

            <button type="submit" class="btn btn-primary">
                Salvar frete
            </button>

        </div>

        <?php if ($mensagem !== ''): ?>

            <div
                class="mensagem <?= htmlspecialchars($tipoMensagem) ?> alert <?= $tipoMensagem === 'sucesso' ? 'alert-success' : 'alert-danger' ?>"
                role="alert"
            >
                <?= htmlspecialchars($mensagem) ?>
            </div>

        <?php endif; ?>

    </form>

</section>

<script>

// Adiciona uma nova linha de produto
document.getElementById('adicionar-produto').addEventListener('click', function () {

    const lista = document.getElementById('lista-produtos-frete');

    const primeiraLinha = lista.querySelector('.linha-produto-frete');

    const novaLinha = primeiraLinha.cloneNode(true);

    // Limpa os campos da nova linha
    novaLinha.querySelector('select').selectedIndex = 0;
    novaLinha.querySelector('input').value = 1;

    // Habilita o botão de remover
    novaLinha.querySelector('.remover-produto').disabled = false;

    lista.appendChild(novaLinha);

    atualizarBotoesRemover();

});

// Remove uma linha de produto
function removerProduto(botao) {

    const linha = botao.closest('.linha-produto-frete');

    linha.remove();

    atualizarBotoesRemover();

}

// Atualiza os botões para impedir que a última linha seja removida
function atualizarBotoesRemover() {

    const linhas = document.querySelectorAll('.linha-produto-frete');

    linhas.forEach(function (linha) {

        const botao = linha.querySelector('.remover-produto');

        botao.disabled = linhas.length === 1;

    });

}

</script>

<?php
require_once '../includes/footer.php';
?>


