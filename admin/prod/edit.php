<?php
include("../config.inc.php");
include("../session.php");
validaSessao();
validaUserProduto();

$link = mysqli_connect("localhost", "root", "", "sistema");
if (!$link) {
    die("Erro de conexão: " . mysqli_connect_error());
}

$id = "";
$error = "";

if (isset($_GET["id"]) && !empty($_GET["id"]) && is_numeric($_GET["id"])) {
    $id = mysqli_real_escape_string($link, $_GET["id"]);
} elseif (isset($_POST["id"]) && !empty($_POST["id"]) && is_numeric($_POST["id"])) {
    $id = mysqli_real_escape_string($link, $_POST["id"]);
} else {
    header("Location: /sistema/admin/prod/");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    extract($_POST);

    if (!$nome) {
        $error .= " Nome obrigatório! ";
    }
    if (!$preco) {
        $error .= " Preço obrigatório! ";
    }
    if (!$preco_compra) {
        $error .= " Preço de compra obrigatório! ";
    }
    if (!$estoque && $estoque !== "0") {
        $error .= " Estoque obrigatório! ";
    }
    if (!isset($ativo) || $ativo === "") {
        $error .= " Ativo obrigatório! ";
    }
    if (!$id_conta) {
        $error .= " Conta obrigatória! ";
    }
    if (!$id_categoria) {
        $error .= " Categoria obrigatória! ";
    }

    if (empty($error)) {
        $nome_seguro = mysqli_real_escape_string($link, $nome);
        $preco_seguro = mysqli_real_escape_string($link, $preco);
        $active = ($ativo === "1") ? 'Y' : 'N';

        $sql = "UPDATE product SET 
            name = '$nome_seguro', 
            sell_price = '$preco_seguro', 
            buy_price = '" . mysqli_real_escape_string($link, $preco_compra) . "', 
            stock = '" . mysqli_real_escape_string($link, $estoque) . "', 
            active = '" . mysqli_real_escape_string($link, $active) . "', 
            id_account = '" . mysqli_real_escape_string($link, $id_conta) . "', 
            id_category = '" . mysqli_real_escape_string($link, $id_categoria) . "',
            description = '" . mysqli_real_escape_string($link, $descricao) . "'
            WHERE id_product = '$id'";
        if (mysqli_query($link, $sql)) {
            header("Location: /sistema/admin/prod/");
            exit;
        } else {
            $error = "Erro ao atualizar produto: " . mysqli_error($link);
        }
    }
}

$sql = "SELECT * FROM product WHERE id_product = '$id'";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) === 0) {
    header("Location: /sistema/admin/prod/");
    exit;
}
$row = mysqli_fetch_assoc($result);
extract($row);

mysqli_close($link);

include("../../header.php");
include("../menu.php");
?>

<h3>EDITAR PRODUTO</h3>

<?php
if (!empty($error)) {
    echo "<span style='color: red; font-style: italic;'>" . $error . "</span>";
}
?>

<form method="POST">
    <input type="hidden" name="id" value="<?= htmlspecialchars($id); ?>">
    <table>
        <tr>
            <td style="text-align: right;">Nome:</td>
            <td>
                <input type="text" name="nome" value="<?= htmlspecialchars($name); ?>">
            </td>
        </tr>
        <tr>
            <td style="text-align: right;">Preço de compra:</td>
            <td>
                <input type="text" name="preco_compra" value="<?= isset($buy_price) ? htmlspecialchars($buy_price) : ''; ?>">
            </td>
        </tr>
        <tr>
            <td style="text-align: right;">Preço:</td>
            <td>
                <input type="text" name="preco" value="<?= htmlspecialchars($sell_price); ?>">
            </td>
        </tr>
        <tr>
            <td style="text-align: right;">Estoque:</td>
            <td>
                <input type="number" name="estoque" value="<?= isset($stock) ? htmlspecialchars($stock) : ''; ?>">
            </td>
        </tr>
        <tr>
            <td style="text-align: right;">Descrição:</td>
            <td>
                <textarea name="descricao"><?= isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
            </td>
        </tr>
        <tr>
            <td style="text-align: right;">Ativo:</td>
            <td>
                <select name="ativo">
                    <option value="1" <?= (isset($active) && $active == "Y") ? "selected" : ""; ?>>Sim</option>
                    <option value="0" <?= (isset($active) && $active == "N") ? "selected" : ""; ?>>Não</option>
                </select>
            </td>
        </tr>
        <tr>
            <?php
            $default_id = isset($id_account) ? htmlspecialchars($id_account) : '';
            ?>
            <td style="text-align: right;">Conta:</td>
            <td>
                <!-- mostra o id da conta mas disabled para não ser editável -->
                <input type="number" value="<?= htmlspecialchars($default_id); ?>" disabled="true">
                <!-- campo oculto para garantir que o id_conta seja submetido no POST -->
                <input type="hidden" name="id_conta" value="<?= htmlspecialchars($default_id); ?>">
            </td>
        </tr>
        <tr>
            <td style="text-align: right;">Categoria:</td>
            <td>
                <?php
                // buscar categorias (nome e id) e montar select
                $link = mysqli_connect("localhost", "root", "", "sistema");
                $categories = [];
                if ($link) {
                    $res = mysqli_query($link, "SELECT id_category, name FROM category ORDER BY name");
                    if ($res) {
                        while ($row = mysqli_fetch_assoc($res)) {
                            $categories[] = $row;
                        }
                        mysqli_free_result($res);
                    }
                }

                // valor selecionado previamente (pode ser id)
                $selected_id = isset($id_category) ? htmlspecialchars($id_category) : '';
                ?>
                <select id="categoria_select" name="categoria_nome">
                    <option value="">-- Escolha uma categoria --</option>
                    <?php foreach ($categories as $c): ?>
                        <option data-id="<?= htmlspecialchars($c['id_category']); ?>"
                            <?= ($selected_id !== '' && $selected_id == $c['id_category']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($c['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- campo oculto que será submetido com o id da categoria -->
                <input type="hidden" id="id_categoria" name="id_categoria" value="<?= htmlspecialchars($selected_id) ?>">
            </td>

            <script>
                // popula o campo oculto com o id correspondente ao nome escolhido
                (function() {
                    var sel = document.getElementById('categoria_select');
                    var hid = document.getElementById('id_categoria');

                    function sync() {
                        var opt = sel.options[sel.selectedIndex];
                        hid.value = opt && opt.dataset ? (opt.dataset.id || '') : '';
                    }
                    if (sel) {
                        sel.addEventListener('change', sync);
                        // sincroniza inicialmente (caso já venha selecionado)
                        sync();
                    }
                })();
            </script>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">
                <input type="submit" name="submit" value="Atualizar">
            </td>
        </tr>
    </table>
</form>

<?php
include("../../footer.php");
?>