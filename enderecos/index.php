
<?php

require_once '../config/conexao.php';

$mensagem = '';
$tipoMensagem = '';

$tipo = '';
$nome = '';
$cep = '';
$rua = '';
$numero = '';
$cidade = '';
$estado = '';
$bairro = '';
$complemento = '';

$enderecoEdicao = null;

// Identifica se existe um endereço selecionado para edição
$idEdicao = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);

if ($idEdicao) {

    $sqlEdicao = "SELECT * FROM enderecos WHERE id = :id";
    $consultaEdicao = $conexao->prepare($sqlEdicao);

    $consultaEdicao->execute([
        ':id' => $idEdicao
    ]);

    $enderecoEdicao = $consultaEdicao->fetch(PDO::FETCH_ASSOC);

    if ($enderecoEdicao) {

        $tipo = $enderecoEdicao['tipo'];
        $nome = $enderecoEdicao['nome'];
        $cep = $enderecoEdicao['cep'];
        $rua = $enderecoEdicao['rua'];
        $numero = $enderecoEdicao['numero'];
        $cidade = $enderecoEdicao['cidade'];
        $estado = $enderecoEdicao['estado'];
        $bairro = $enderecoEdicao['bairro'];
        $complemento = $enderecoEdicao['complemento'];

    } else {

        $mensagem = 'Endereço não encontrado.';
        $tipoMensagem = 'erro';

    }
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idEndereco = $_POST['id_endereco'] ?? '';

    $tipo = $_POST['tipo'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $cep = trim($_POST['cep'] ?? '');
    $rua = trim($_POST['rua'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $estado = strtoupper(trim($_POST['estado'] ?? ''));
    $bairro = trim($_POST['bairro'] ?? '');
    $complemento = trim($_POST['complemento'] ?? '');

    // Valida os campos obrigatórios
    if (
        !in_array($tipo, ['origem', 'destino'], true) ||
        $nome === '' ||
        $cep === '' ||
        $rua === '' ||
        $numero === '' ||
        $cidade === '' ||
        !preg_match('/^[A-Z]{2}$/', $estado)
    ) {

        $mensagem = 'Preencha corretamente todos os campos obrigatórios.';
        $tipoMensagem = 'erro';

    } else {

        try {

            if ($idEndereco !== '' && ctype_digit((string) $idEndereco)) {

                // Atualiza um endereço existente
                $sql = "UPDATE enderecos SET
                        tipo = :tipo,
                        nome = :nome,
                        cep = :cep,
                        rua = :rua,
                        numero = :numero,
                        cidade = :cidade,
                        estado = :estado,
                        bairro = :bairro,
                        complemento = :complemento
                        WHERE id = :id";

                $consulta = $conexao->prepare($sql);

                $consulta->execute([
                    ':tipo' => $tipo,
                    ':nome' => $nome,
                    ':cep' => $cep,
                    ':rua' => $rua,
                    ':numero' => $numero,
                    ':cidade' => $cidade,
                    ':estado' => $estado,
                    ':bairro' => $bairro !== '' ? $bairro : null,
                    ':complemento' => $complemento !== '' ? $complemento : null,
                    ':id' => $idEndereco
                ]);

                $mensagem = 'Endereço atualizado com sucesso!';
                $tipoMensagem = 'sucesso';

            } else {

                // Cadastra um novo endereço
                $sql = "INSERT INTO enderecos
                        (tipo, nome, cep, rua, numero, cidade, estado, bairro, complemento)
                        VALUES
                        (:tipo, :nome, :cep, :rua, :numero, :cidade, :estado, :bairro, :complemento)";

                $consulta = $conexao->prepare($sql);

                $consulta->execute([
                    ':tipo' => $tipo,
                    ':nome' => $nome,
                    ':cep' => $cep,
                    ':rua' => $rua,
                    ':numero' => $numero,
                    ':cidade' => $cidade,
                    ':estado' => $estado,
                    ':bairro' => $bairro !== '' ? $bairro : null,
                    ':complemento' => $complemento !== '' ? $complemento : null
                ]);

                $mensagem = 'Endereço cadastrado com sucesso!';
                $tipoMensagem = 'sucesso';
            }

            // Limpa os campos após salvar
            $tipo = '';
            $nome = '';
            $cep = '';
            $rua = '';
            $numero = '';
            $cidade = '';
            $estado = '';
            $bairro = '';
            $complemento = '';

            $enderecoEdicao = null;

        } catch (PDOException $erro) {

            $mensagem = 'Não foi possível salvar o endereço.';
            $tipoMensagem = 'erro';

        }
    }
}

// Busca os endereços cadastrados
$sqlEnderecos = "
    SELECT id, tipo, nome, cep, rua, numero, cidade, estado
    FROM enderecos
    ORDER BY id DESC
";

$consultaEnderecos = $conexao->query($sqlEnderecos);

$enderecos = $consultaEnderecos->fetchAll(PDO::FETCH_ASSOC);

// Inclui o cabeçalho
require_once '../includes/header.php';

?>

<header class="cabecalho">

    <h1>Cadastro de Endereços</h1>

    <p>Cadastre e gerencie os endereços de origem e destino das operações logísticas.</p>

</header>

<section class="formulario-produto">

    <h2>
        <?= $enderecoEdicao ? 'Editar endereço' : 'Informações do endereço' ?>
    </h2>

    <form action="" method="POST">

        <?php if ($enderecoEdicao): ?>

            <input
                type="hidden"
                name="id_endereco"
                value="<?= htmlspecialchars((string) $enderecoEdicao['id']) ?>"
            >

        <?php endif; ?>

        <div class="linha-campos">

            <div class="campo">

                <label class="form-label" for="tipo">
                    Tipo de endereço: *
                </label>

                <select
                    class="form-select"
                    id="tipo"
                    name="tipo"
                    required
                >

                    <option value="">Selecione o tipo de endereço.</option>

                    <option value="origem" <?= $tipo === 'origem' ? 'selected' : '' ?>>
                        Origem
                    </option>

                    <option value="destino" <?= $tipo === 'destino' ? 'selected' : '' ?>>
                        Destino
                    </option>

                </select>

            </div>

            <div class="campo">

                <label class="form-label" for="nome">
                    Nome do endereço: *
                </label>

                <input
                    class="form-control"
                    type="text"
                    id="nome"
                    name="nome"
                    placeholder="Ex.: Matriz, Centro de Distribuição."
                    value="<?= htmlspecialchars((string) $nome) ?>"
                    required
                >

            </div>

        </div>

        <div class="linha-campos">

            <div class="campo">

                <label class="form-label" for="cep">
                    CEP: *
                </label>

                <input
                    class="form-control"
                    type="text"
                    id="cep"
                    name="cep"
                    placeholder="Digite o CEP."
                    value="<?= htmlspecialchars((string) $cep) ?>"
                    required
                >

            </div>

        </div>

        <div class="linha-campos">

            <div class="campo">

                <label class="form-label" for="rua">
                    Rua / Avenida: *
                </label>

                <input
                    class="form-control"
                    type="text"
                    id="rua"
                    name="rua"
                    placeholder="Digite o nome da rua."
                    value="<?= htmlspecialchars((string) $rua) ?>"
                    required
                >

            </div>

            <div class="campo">

                <label class="form-label" for="numero">
                    Número: *
                </label>

                <input
                    class="form-control"
                    type="text"
                    id="numero"
                    name="numero"
                    placeholder="Nº"
                    value="<?= htmlspecialchars((string) $numero) ?>"
                    required
                >

            </div>

        </div>

        <div class="linha-campos">

            <div class="campo">

                <label class="form-label" for="cidade">
                    Cidade: *
                </label>

                <input
                    class="form-control"
                    type="text"
                    id="cidade"
                    name="cidade"
                    placeholder="Digite a cidade."
                    value="<?= htmlspecialchars((string) $cidade) ?>"
                    required
                >

            </div>

            <div class="campo">

                <label class="form-label" for="estado">
                    Estado: *
                </label>

                <input
                    class="form-control"
                    type="text"
                    id="estado"
                    name="estado"
                    maxlength="2"
                    placeholder="Ex.: PR"
                    value="<?= htmlspecialchars((string) $estado) ?>"
                    required
                >

            </div>

        </div>

        <div class="linha-campos">

            <div class="campo">

                <label class="form-label" for="bairro">
                    Bairro:
                </label>

                <input
                    class="form-control"
                    type="text"
                    id="bairro"
                    name="bairro"
                    placeholder="Digite o bairro."
                    value="<?= htmlspecialchars((string) $bairro) ?>"
                >

            </div>

            <div class="campo">

                <label class="form-label" for="complemento">
                    Complemento:
                </label>

                <input
                    class="form-control"
                    type="text"
                    id="complemento"
                    name="complemento"
                    placeholder="Ex.: Sala 02, Galpão A."
                    value="<?= htmlspecialchars((string) $complemento) ?>"
                >

            </div>

        </div>

        <div class="botoes-formulario">

            <?php if ($enderecoEdicao): ?>

                <a href="index.php" class="btn btn-outline-secondary">
                    Cancelar edição
                </a>

                <button type="submit" class="btn btn-primary">
                    Salvar alterações
                </button>

            <?php else: ?>

                <button type="reset" class="btn btn-outline-secondary">
                    Limpar endereço
                </button>

                <button type="submit" class="btn btn-primary">
                    Salvar endereço
                </button>

            <?php endif; ?>

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

<section class="lista-produtos">

    <h2>Endereços cadastrados</h2>

    <div class="table-responsive">

        <table class="table table-hover align-middle">

            <thead class="table-light">

                <tr>
                    <th>Tipo</th>
                    <th>Nome</th>
                    <th>CEP</th>
                    <th>Endereço</th>
                    <th>Cidade</th>
                    <th>Estado</th>
                    <th>Ações</th>
                </tr>

            </thead>

            <tbody>

                <?php if (count($enderecos) > 0): ?>

                    <?php foreach ($enderecos as $endereco): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars(
                                    $endereco['tipo'] === 'origem' ? 'Origem' : 'Destino'
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($endereco['nome']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($endereco['cep']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($endereco['rua'] . ', ' . $endereco['numero']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($endereco['cidade']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($endereco['estado']) ?>
                            </td>

                            <td>

                                <a
                                    href="index.php?editar=<?= (int) $endereco['id'] ?>"
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    Editar
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="7" class="text-center">
                            Nenhum endereço cadastrado.
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


