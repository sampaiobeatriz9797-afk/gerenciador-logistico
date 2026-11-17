
<?php

require_once 'config/conexao.php';

// Consulta a quantidade de produtos cadastrados
$sql = "SELECT COUNT(*) FROM produtos";
$consulta = $conexao->query($sql);
$totalProdutos = $consulta->fetchColumn();

// Consulta a quantidade de transportadoras cadastradas
$sqlTransportadoras = "SELECT COUNT(*) FROM transportadoras";
$consultaTransportadoras = $conexao->query($sqlTransportadoras);
$totalTransportadoras = $consultaTransportadoras->fetchColumn();

// Consulta a quantidade de fretes registrados
$sqlFretes = "SELECT COUNT(*) FROM fretes";
$consultaFretes = $conexao->query($sqlFretes);
$totalFretes = $consultaFretes->fetchColumn();

// Consulta a quantidade de fretes em andamento
$sqlFretesAndamento = "
    SELECT COUNT(*)
    FROM fretes
    WHERE status = 'Em andamento'
";

$consultaFretesAndamento = $conexao->query($sqlFretesAndamento);
$totalFretesAndamento = $consultaFretesAndamento->fetchColumn();

// Consulta os últimos fretes cadastrados
$sqlUltimosFretes = "
    SELECT
        f.id,
        GROUP_CONCAT(DISTINCT p.nome SEPARATOR ', ') AS produtos,
        t.nome AS transportadora,
        f.status
    FROM fretes f
    LEFT JOIN transportadoras t
        ON f.transportadora_id = t.id
    LEFT JOIN itens_frete i
        ON f.id = i.frete_id
    LEFT JOIN produtos p
        ON i.produto_id = p.id
    GROUP BY f.id, t.nome, f.status
    ORDER BY f.id DESC
    LIMIT 5
";

$consultaUltimosFretes = $conexao->query($sqlUltimosFretes);

$ultimosFretes = $consultaUltimosFretes->fetchAll(PDO::FETCH_ASSOC);

// Inclui o cabeçalho compartilhado
require_once 'includes/header.php';

?>

<header class="cabecalho">

    <h1>Visão geral</h1>

    <p>Acompanhe as operações logísticas da sua empresa.</p>

</header>

<!-- Indicadores do painel -->
<section class="indicadores">

    <div class="card-indicador">

        <p>Produtos cadastrados</p>

        <h2>
            <?= (int) $totalProdutos ?>
        </h2>

    </div>

    <div class="card-indicador">

        <p>Transportadoras</p>

        <h2>
            <?= (int) $totalTransportadoras ?>
        </h2>

    </div>

    <div class="card-indicador">

        <p>Fretes registrados</p>

        <h2>
            <?= (int) $totalFretes ?>
        </h2>

    </div>

    <div class="card-indicador">

        <p>Fretes em andamento</p>

        <h2>
            <?= (int) $totalFretesAndamento ?>
        </h2>

    </div>

</section>

<!-- Últimos fretes cadastrados -->
<section class="ultimos-fretes">

    <h3>Últimos fretes cadastrados</h3>

    <div class="tabela-fretes">

        <table>

            <thead>

                <tr>
                    <th>Frete</th>
                    <th>Produtos</th>
                    <th>Transportadoras</th>
                    <th>Status</th>
                </tr>

            </thead>

            <tbody>

                <?php if (count($ultimosFretes) > 0): ?>

                    <?php foreach ($ultimosFretes as $frete): ?>

                        <tr>

                            <td>
                                <?= (int) $frete['id'] ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($frete['produtos'] ?? 'Não informado') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($frete['transportadora'] ?? 'Não informada') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($frete['status']) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="4">
                            Nenhum frete cadastrado.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

        <?php if (count($ultimosFretes) === 0): ?>

            <div class="estado-vazio">

                <div class="icone-caminhao">🚚</div>

                <p>Nenhum frete cadastrado</p>

            </div>

        <?php endif; ?>

    </div>

</section>

<?php

// Inclui o rodapé compartilhado
require_once 'includes/footer.php';

?>

