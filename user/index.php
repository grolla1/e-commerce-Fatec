<?php
include("./config.inc.php");
include("../header.php");
include("../sidebar.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION["CONTA_ID"])) {
    echo '<a href="/sistema/user/login.php" style="color: black;">Login</a>';
} else {
    echo '<a href="/sistema/user/logout.php" style="color: black;">Logout</a>';
}
?>

<form>

    <br><br>

    <br><br>
    Pesquisar produto:
    <br>
    <div class="select-container">
        <input type="text" id="kw_produto" name="kw_produto" class="select-search" placeholder="Digite para filtrar..."
            value="<?= (isset($_GET['kw_produto']) && $_GET['kw_produto']) ? $_GET['kw_produto'] : '' ?>">
    </div>
    <br>

    <label>Categoria:</label><br>
    <div class="select-container">
        <input type="text" id="kw_categoria" name="kw_categoria" class="select-search" placeholder="Digite para filtrar..."
            value="<?= (isset($_GET['kw_categoria']) && $_GET['kw_categoria']) ? $_GET['kw_categoria'] : '' ?>">
        <div id="dropdown" class="dropdown-list"></div>
    </div>

    <script>
        // Preenche categorias via PHP
        const categorias = [
            <?php
            $catLink = mysqli_connect('localhost', 'root', '', 'sistema');
            if ($catLink) {
                $csql = "SELECT name FROM category ORDER BY name";
                if ($cres = mysqli_query($catLink, $csql)) {
                    $values = [];
                    while ($crow = mysqli_fetch_assoc($cres)) {
                        $values[] = "'" . addslashes($crow['name']) . "'";
                    }
                    echo implode(',', $values);
                    mysqli_free_result($cres);
                }
                mysqli_close($catLink);
            }
            ?>
        ];

        // JS para dropdown filtrável
        const input = document.getElementById('kw_categoria');
        const dropdown = document.getElementById('dropdown');

        function showDropdown(filtered) {
            dropdown.innerHTML = '';
            filtered.forEach(item => {
                const div = document.createElement('div');
                div.textContent = item;
                div.onclick = () => {
                    input.value = item;
                    dropdown.style.display = 'none';
                };
                dropdown.appendChild(div);
            });
            dropdown.style.display = filtered.length ? 'block' : 'none';
        }

        input.addEventListener('input', () => {
            const val = input.value.toLowerCase();
            const filtered = categorias.filter(c => c.toLowerCase().includes(val));
            showDropdown(filtered);
        });

        input.addEventListener('focus', () => showDropdown(categorias));
        document.addEventListener('click', e => {
            if (!e.target.closest('.select-container')) dropdown.style.display = 'none';
        });
    </script>
    <button id="btn-buscar" type="submit">Buscar</button>
</form>

<?php
$link = mysqli_connect('localhost', 'root', '', 'sistema');
if (!$link) {
    die('Erro ao conectar ao banco de dados.');
}

// Consulta inteligente: filtra por produto (kw_produto) e/ou categoria (kw_categoria).
$sql = "SELECT p.* FROM product p LEFT JOIN category c ON p.id_category = c.id_category";
$where = [];
$params = [];
$types = '';

// filtro por nome do produto
if (!empty($_GET['kw_produto'])) {
    $where[] = "p.name LIKE ?";
    $types .= 's';
    $params[] = '%' . $_GET['kw_produto'] . '%';
}

// filtro por nome da categoria
if (!empty($_GET['kw_categoria'])) {
    $where[] = "c.name LIKE ?";
    $types .= 's';
    $params[] = '%' . $_GET['kw_categoria'] . '%';
}

$where[] = "p.active = 'Y'";

if (!empty($where)) {
    // quando há mais de um filtro, aplicamos todos (AND). Se quiser OR, troque " AND " por " OR ".
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY p.name";
// Usar prepared statement para evitar SQL injection
$stmt = mysqli_prepare($link, $sql);
if ($stmt) {
    if (!empty($params)) {
        // bind_param requer referências
        $bind_names = [];
        $bind_names[] = &$types;
        for ($i = 0; $i < count($params); $i++) {
            $bind_names[] = &$params[$i];
        }
        call_user_func_array([$stmt, 'bind_param'], $bind_names);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    // $result será usado abaixo — pode ter 0 linhas
} else {
    // fallback simples caso prepare falhe
    $result = mysqli_query($link, $sql);
}
if (mysqli_num_rows($result) > 0) {
?>
    <table border="1">
        <tr>
            <th>Nome</th>
            <th>Preço</th>
            <th>Carrinho</th>
        </tr>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
        ?>
            <tr>
                <td><?= $row["name"]; ?></td>
                <td><?= $row["sell_price"]; ?></td>
                <td>
                    <a href="/sistema/user/carrinho.php?a=<?= $row["id_product"]; ?>" style="color: black;">Adicionar +</a>
                </td>
            </tr>
        <?php
        }
        ?>
    </table>
    <br>
    <br><a href="/sistema/user/carrinho.php">Carrinho</a>
<?php
} else {
    echo "Sem Produtos";
}
?>



<?php
include("../footer.php");
?>