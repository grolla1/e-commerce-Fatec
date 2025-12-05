<?php
include("./config.inc.php");
include("../header.php");
include("./session.php");
validaSessao()
?>

<h3> CARRINHO </h3>

<?php

if (isset($_GET["ok"]) && $_GET["ok"] == 1) {
    echo '<div class="sucesso">Compra finalizada com sucesso!</div>';
}

if (isset($_GET["a"])) {
    if (isset($_COOKIE["carrinho"])) {
        if (strpos($_COOKIE["carrinho"], "'" . $_GET["a"] . "'") === false) {
            setcookie(
                "carrinho",
                $_COOKIE["carrinho"] . ",'" . $_GET["a"] . "'",
                time() + 60 * 60 * 24 * 30
            );
        }
    } else {
        setcookie("carrinho", "'" . $_GET["a"] . "'", time() + 60 * 60 * 24 * 30);
    }
    header("Location: /sistema/user/carrinho.php");
    exit;
} else if (isset($_GET["r"])) {
    if (isset($_COOKIE["carrinho"])) {
        if (strpos($_COOKIE["carrinho"], "'" . $_GET["r"] . "'") !== false) {
            $carrinho = $_COOKIE["carrinho"];
            $carrinho = str_replace(",'" . $_GET["r"] . "',", ",", $carrinho);
            $carrinho = str_replace("'" . $_GET["r"] . "',", "", $carrinho);
            $carrinho = str_replace(",'" . $_GET["r"] . "'", "", $carrinho);
            $carrinho = str_replace("'" . $_GET["r"] . "'", "", $carrinho);
            setcookie("carrinho", $carrinho, time() + 60 * 60 * 24 * 30);
        }
    }
    header("Location: /sistema/user/carrinho.php");
    exit;
}
?>
<html>

<head>
    <title>carrinho</title>
</head>

<body>
    <a href="/sistema/user/">Index</a><br><br>
    <div class="carrinho-container">

<?php
// FINALIZAR COMPRA
if (isset($_GET["finalizar"])) {

    if (!isset($_COOKIE["carrinho"])) {
        header("Location: /sistema/user/carrinho.php");
        exit;
    }

    $link = mysqli_connect("localhost", "root", "", "sistema");

    // Buscar itens
    $sql = "SELECT * FROM product WHERE id_product IN (" . $_COOKIE["carrinho"] . ")";
    $result = mysqli_query($link, $sql);

    $subtotal = 0;
    $produtos = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $produtos[] = $row;
        $subtotal += $row["sell_price"];
    }

    // Criar venda
    $idAccount = $_SESSION["CONTA_ID"]; // seu ID da sessão
    mysqli_query($link, "INSERT INTO sale (total_value, id_account) VALUES ($subtotal, $idAccount)");
    $idSale = mysqli_insert_id($link);

    // Registrar itens
    foreach ($produtos as $p) {

        $idProd = $p["id_product"];

        // Inserir item
        mysqli_query($link,
            "INSERT INTO sale_product (id_sale, id_product, quantity)
            VALUES ($idSale, $idProd, 1)"
        );

        // Baixar estoque
        mysqli_query($link,
            "UPDATE product SET stock = stock - 1 WHERE id_product = $idProd"
        );
    }

    // limpar carrinho
    setcookie("carrinho", "", time() - 3600);

    // redirecionar
    header("Location: /sistema/user/carrinho.php?ok=1");
    exit;
}


if (isset($_COOKIE["carrinho"])) {

    $link = mysqli_connect("localhost", "root", "", "sistema");
    $sql = "SELECT * FROM product WHERE id_product IN (" . $_COOKIE["carrinho"] . ") ORDER BY name";
    $result = mysqli_query($link, $sql);

    $subtotal = 0;

    if ($result) {

        while ($row = mysqli_fetch_assoc($result)) {

            $subtotal += $row["sell_price"];

            echo '
            <div class="item">
                <div class="item-name">'.$row["name"].' — R$ '.number_format($row["sell_price"],2,",",".").'</div>
                <a class="remove-btn" href="/sistema/user/carrinho.php?r='.$row["id_product"].'">Remover</a>
            </div>
            ';
        }

        echo '<div class="total-section">Subtotal: R$ '.number_format($subtotal,2,",",".").'</div>';
        echo '<a class="finalizar-btn" href="/sistema/user/carrinho.php?finalizar=1">Finalizar Compra</a>';
    }
} else {
    echo "Carrinho vazio!<br>";
}
?>

</div>

    <br><a href="/sistema/user/">Index</a>
</body>

</html>
<?php
include("../footer.php");
?>