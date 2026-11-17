
<?php

require_once '../config/conexao.php';

// Define o título da página
$tituloPagina = 'Relatórios';

// Inicializa as variáveis
$dataSelecionada = '';
$fretes = [];
$mensagem = '';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dataSelecionada = $_POST['data_frete'] ?? '';

    // Verifica se uma data foi selecionada
    if ($dataSelecionada !== '') {

        try {

            $sql = "
                SELECT
                    f.id,
                    GROUP_CONCAT(DISTINCT p.nome SEPARATOR ', ') AS produtos,
                    o.nome AS origem,
                    d.nome AS destino,
                    t.nome AS transportadora,
                    f.data_frete,
                    f.status

                FROM fretes f

                INNER JOIN itens_frete i
                    ON f.id = i.frete_id

                INNER JOIN produtos p
                    ON i.produto_id = p.id

                INNER JOIN enderecos o
                    ON f.endereco_origem_id = o.id

                INNER JOIN enderecos d
                    ON f.endereco_destino_id = d.id

                INNER JOIN transportadoras t
                    ON f.transportadora_id = t.id

                WHERE f.data_frete = :data_frete

                GROUP BY
                    f.id,
                    o.nome,
                    d.nome,
                    t.nome,
                    f.data_frete,
                    f.status

                ORDER BY f.id DESC
            ";

            $consulta = $conexao->prepare($sql);

            $consulta->execute([
                ':data_frete' => $dataSelecionada
            ]);

            $fretes = $consulta->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $erro) {

            $mensagem = 'Erro ao consultar os fretes.';

        }

    }

}

// Inclui o cabeçalho da página
require_once '../includes/header.php';

?>

<!-- Cabeçalho -->
<header class="cabecalho">

    <h1>Relatórios de fretes</h1>

    <p>Consulte e gere relatórios dos fretes registrados por período.</p>

</header>

<!-- Filtro dos relatórios -->
<section class="card-relatorio">

    <h2>Filtro dos relatórios</h2>

    <form method="POST" action="">

        <div class="campo-data">

            <label class="form-label" for="data_frete">
                Data do relatório:
            </label>

            <input
                class="form-control"
                type="date"
                id="data_frete"
                name="data_frete"
                value="<?= htmlspecialchars($dataSelecionada) ?>"
                required
            >

        </div>

        <button type="submit" class="btn btn-primary">
            Gerar relatório
        </button>

    </form>

</section>

<!-- Resultados dos relatórios -->
<section class="card-resultados">

    <div class="titulo-resultados">

        <h2>Fretes encontrados</h2>

        <button
            type="button"
            class="btn btn-outline-primary"
            onclick="window.print()"
        >
            Baixar relatório PDF
        </button>

    </div>

    <div class="table-responsive">

        <table class="table table-hover align-middle">

            <thead class="table-light">

                <tr>
                    <th>Código</th>
                    <th>Produto</th>
                    <th>Origem</th>
                    <th>Destino</th>
                    <th>Transportadora</th>
                    <th>Status</th>
                </tr>

            </thead>

            <tbody>

                <?php if ($mensagem !== ''): ?>

                    <tr>
                        <td colspan="6" class="text-center">
                            <?= htmlspecialchars($mensagem) ?>
                        </td>
                    </tr>

                <?php elseif (count($fretes) > 0): ?>

                    <?php foreach ($fretes as $frete): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($frete['id']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($frete['produtos']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($frete['origem']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($frete['destino']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($frete['transportadora']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($frete['status']) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php elseif ($dataSelecionada !== ''): ?>

                    <tr>
                        <td colspan="6" class="text-center">
                            Nenhum frete encontrado para esta data.
                        </td>
                    </tr>

                <?php else: ?>

                    <tr>
                        <td colspan="6" class="text-center">
                            Selecione uma data para consultar os fretes.
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

