
<?php

// Identifica o caminho da página atual
$caminhoAtual = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gerenciador Logístico</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- CSS personalizado do projeto -->
    <link
        rel="stylesheet"
        href="/gerenciador-logistico/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>"
    >
</head>

<body>

    <div class="layout">

        <aside class="menu-lateral">

            <h2>Gestão Logística</h2>

            <nav>
                <ul>

                    <li>
                        <a
                            href="/gerenciador-logistico/index.php"
                            class="<?= $caminhoAtual === 'gerenciador-logistico/index.php' ? 'ativo' : '' ?>"
                        >
                            Painel
                        </a>
                    </li>

                    <li>
                        <a
                            href="/gerenciador-logistico/produtos/index.php"
                            class="<?= $caminhoAtual === 'gerenciador-logistico/produtos/index.php' ? 'ativo' : '' ?>"
                        >
                            Produtos
                        </a>
                    </li>

                    <li>
                        <a
                            href="/gerenciador-logistico/enderecos/index.php"
                            class="<?= $caminhoAtual === 'gerenciador-logistico/enderecos/index.php' ? 'ativo' : '' ?>"
                        >
                            Endereços
                        </a>
                    </li>

                    <li>
                        <a
                            href="/gerenciador-logistico/transportadoras/index.php"
                            class="<?= $caminhoAtual === 'gerenciador-logistico/transportadoras/index.php' ? 'ativo' : '' ?>"
                        >
                            Transportadoras
                        </a>
                    </li>

                    <li>
                        <a
                            href="/gerenciador-logistico/fretes/index.php"
                            class="<?= $caminhoAtual === 'gerenciador-logistico/fretes/index.php' ? 'ativo' : '' ?>"
                        >
                            Fretes
                        </a>
                    </li>

                    <li>
                        <a
                            href="/gerenciador-logistico/relatorios/index.php"
                            class="<?= $caminhoAtual === 'gerenciador-logistico/relatorios/index.php' ? 'ativo' : '' ?>"
                        >
                            Relatórios
                        </a>
                    </li>

                </ul>
            </nav>

        </aside>

        <main class="conteudo-principal">



