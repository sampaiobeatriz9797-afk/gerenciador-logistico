
<?php

require_once '../config/conexao.php';

$mensagem = '';
$tipoMensagem = '';

$nome = '';
$cnpj = '';
$telefone = '';
$email = '';
$endereco = '';

$transportadoraEdicao = null;

// Identifica se existe uma transportadora selecionada para edição
$idEdicao = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);

if ($idEdicao) {

    $sqlEdicao = "SELECT * FROM transportadoras WHERE id = :id";
    $consultaEdicao = $conexao->prepare($sqlEdicao);

    $consultaEdicao->execute([
        ':id' => $idEdicao
    ]);

    $transportadoraEdicao = $consultaEdicao->fetch(PDO::FETCH_ASSOC);

    if ($transportadoraEdicao) {

        $nome = $transportadoraEdicao['nome'];
        $cnpj = $transportadoraEdicao['cnpj'];
        $telefone = $transportadoraEdicao['telefone'];
        $email = $transportadoraEdicao['email'];
        $endereco = $transportadoraEdicao['endereco'];

    } else {

        $mensagem = 'Transportadora não encontrada.';
        $tipoMensagem = 'erro';

    }
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idTransportadora = $_POST['id_transportadora'] ?? '';

    $nome = trim($_POST['nome'] ?? '');
    $cnpj = preg_replace('/\D/', '', $_POST['cnpj'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');

    // Valida os campos obrigatórios
    if ($nome === '' || strlen($cnpj) !== 14) {

        $mensagem = 'Preencha o nome e informe um CNPJ com 14 dígitos.';
        $tipoMensagem = 'erro';

    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensagem = 'Informe um endereço de e-mail válido.';
        $tipoMensagem = 'erro';

    } else {

        try {

            if (
                $idTransportadora !== '' &&
                ctype_digit((string) $idTransportadora)
            ) {

                // Atualiza uma transportadora existente
                $sql = "UPDATE transportadoras SET
                        nome = :nome,
                        cnpj = :cnpj,
                        telefone = :telefone,
                        email = :email,
                        endereco = :endereco
                        WHERE id = :id";

                $consulta = $conexao->prepare($sql);

                $consulta->execute([
                    ':nome' => $nome,
                    ':cnpj' => $cnpj,
                    ':telefone' => $telefone !== '' ? $telefone : null,
                    ':email' => $email !== '' ? $email : null,
                    ':endereco' => $endereco !== '' ? $endereco : null,
                    ':id' => $idTransportadora
                ]);

                $mensagem = 'Transportadora atualizada com sucesso!';
                $tipoMensagem = 'sucesso';

            } else {

                // Cadastra uma nova transportadora
                $sql = "INSERT INTO transportadoras
                        (nome, cnpj, telefone, email, endereco)
                        VALUES
                        (:nome, :cnpj, :telefone, :email, :endereco)";

                $consulta = $conexao->prepare($sql);

                $consulta->execute([
                    ':nome' => $nome,
                    ':cnpj' => $cnpj,
                    ':telefone' => $telefone !== '' ? $telefone : null,
                    ':email' => $email !== '' ? $email : null,
                    ':endereco' => $endereco !== '' ? $endereco : null
                ]);

                $mensagem = 'Transportadora cadastrada com sucesso!';
                $tipoMensagem = 'sucesso';
            }

            // Limpa os campos após salvar
            $nome = '';
            $cnpj = '';
            $telefone = '';
            $email = '';
            $endereco = '';

            $transportadoraEdicao = null;

        } catch (PDOException $erro) {

            if ($erro->getCode() === '23000') {

                $mensagem = 'Este CNPJ já está cadastrado.';

            } else {

                $mensagem = 'Não foi possível salvar a transportadora.';

            }

            $tipoMensagem = 'erro';
        }
    }
}

// Busca as transportadoras cadastradas
$sqlTransportadoras = "
    SELECT id, nome, cnpj, telefone, email, endereco
    FROM transportadoras
    ORDER BY id DESC
";

$consultaTransportadoras = $conexao->query($sqlTransportadoras);

$transportadoras = $consultaTransportadoras->fetchAll(PDO::FETCH_ASSOC);

// Inclui o cabeçalho
require_once '../includes/header.php';

?>

<header class="cabecalho">

    <h1>Cadastro de Transportadoras</h1>

    <p>Cadastre e gerencie as transportadoras responsáveis pelo transporte dos produtos.</p>

</header>

<section class="formulario-produto">

    <h2>
        <?= $transportadoraEdicao ? 'Editar transportadora' : 'Informações do transportador' ?>
    </h2>

    <form action="" method="POST">

        <?php if ($transportadoraEdicao): ?>

            <input
                type="hidden"
                name="id_transportadora"
                value="<?= htmlspecialchars((string) $transportadoraEdicao['id']) ?>"
            >

        <?php endif; ?>

        <div class="linha-campos">

            <div class="campo">

                <label class="form-label" for="nome">
                    Nome da transportadora: *
                </label>

                <input
                    class="form-control"
                    type="text"
                    id="nome"
                    name="nome"
                    placeholder="Digite o nome da transportadora"
                    value="<?= htmlspecialchars((string) $nome) ?>"
                    required
                >

            </div>

            <div class="campo">

                <label class="form-label" for="cnpj">
                    CNPJ: *
                </label>

                <input
                    class="form-control"
                    type="text"
                    id="cnpj"
                    name="cnpj"
                    placeholder="00.000.000/0000-00"
                    maxlength="18"
                    value="<?= htmlspecialchars((string) $cnpj) ?>"
                    required
                >

            </div>

        </div>

        <div class="linha-campos">

            <div class="campo">

                <label class="form-label" for="telefone">
                    Telefone:
                </label>

                <input
                    class="form-control"
                    type="text"
                    id="telefone"
                    name="telefone"
                    placeholder="(00) 00000-0000"
                    maxlength="15"
                    value="<?= htmlspecialchars((string) $telefone) ?>"
                >

            </div>

            <div class="campo">

                <label class="form-label" for="email">
                    E-mail:
                </label>

                <input
                    class="form-control"
                    type="email"
                    id="email"
                    name="email"
                    placeholder="exemplo@transportadora.com.br"
                    value="<?= htmlspecialchars((string) $email) ?>"
                >

            </div>

        </div>

        <div class="campo">

            <label class="form-label" for="endereco">
                Endereço:
            </label>

            <input
                class="form-control"
                type="text"
                id="endereco"
                name="endereco"
                placeholder="Digite o endereço completo da transportadora."
                value="<?= htmlspecialchars((string) $endereco) ?>"
            >

        </div>

        <div class="botoes-formulario">

            <?php if ($transportadoraEdicao): ?>

                <a href="index.php" class="btn btn-outline-secondary">
                    Cancelar edição
                </a>

                <button type="submit" class="btn btn-primary">
                    Salvar alterações
                </button>

            <?php else: ?>

                <button type="reset" class="btn btn-outline-secondary">
                    Limpar
                </button>

                <button type="submit" class="btn btn-primary">
                    Salvar
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

    <h2>Transportadoras cadastradas</h2>

    <div class="table-responsive">

        <table class="table table-hover align-middle">

            <thead class="table-light">

                <tr>
                    <th>Nome</th>
                    <th>CNPJ</th>
                    <th>Telefone</th>
                    <th>E-mail</th>
                    <th>Endereço</th>
                    <th>Ações</th>
                </tr>

            </thead>

            <tbody>

                <?php if (count($transportadoras) > 0): ?>

                    <?php foreach ($transportadoras as $transportadora): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($transportadora['nome']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($transportadora['cnpj']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($transportadora['telefone'] ?? '') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($transportadora['email'] ?? '') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($transportadora['endereco'] ?? '') ?>
                            </td>

                            <td>

                                <a
                                    href="index.php?editar=<?= (int) $transportadora['id'] ?>"
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    Editar
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="6" class="text-center">
                            Nenhuma transportadora cadastrada.
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


