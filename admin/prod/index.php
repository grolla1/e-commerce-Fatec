<?php
include("../config.inc.php");
include("../session.php");
validaSessao();
include("../../header.php");
include("../menu.php");
?>

<h3>PRODUTOS</h3>

<a href="/sistema/admin/prod/add.php" style="color: black;">+ Adicionar</a>

<br><br>
<table border="1">
	<tr>
		<th>Nome</th>
		<th>Preço</th>
		<th>Descrição</th>
		<th>Ação</th>
	</tr>
	<?php
	$default_id = isset($id_conta)
    	? $id_conta
    	: (isset($_SESSION['id'])
        	? $_SESSION['id']
        	: (isset($_SESSION['CONTA_ID']) ? $_SESSION['CONTA_ID'] : ''));


	$link = mysqli_connect("localhost", "root", "", "sistema");
	$sql = "SELECT id_product, name, sell_price, description, id_account FROM product ORDER BY name;";
	$result = mysqli_query($link, $sql);
	while ($row = mysqli_fetch_assoc($result)) {
		?>
		<tr>
			<td><?=$row["name"];?></td>
			<td><?=$row["sell_price"];?></td>
			<td><?=$row["description"];?></td>
			<td>
    		<?php if ($row["id_account"] == $default_id) { ?>
        		<a href="/sistema/admin/prod/edit.php?id=<?= $row['id_product']; ?>" style="color: black;">Editar</a> |
        		<a href="/sistema/admin/prod/del.php?id=<?= $row['id_product']; ?>" style="color: black;">Excluir</a>
			<?php } else { ?>
        		<span style="color: gray;">(Sem permissão)</span>
    		<?php } ?>
</td>
		</tr>
		<?php
	}
	?>
</table>
<br><br>

<a href="/sistema/admin/prod/add.php" style="color: black;">+ Adicionar</a>

<?php
include("../../footer.php");
?>