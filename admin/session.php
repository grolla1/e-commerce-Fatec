<?php
function contaValida($username, $password) {
	$link = mysqli_connect("localhost", "root", "", "sistema");
	$sql = "SELECT active FROM account WHERE username = '".$username."' AND password = PASSWORD('$password')";
	$result = mysqli_query($link, $sql);
	if ($result) {
		if ($row = mysqli_fetch_assoc($result)) {
			if ($row["active"] != "Y") {
				return false;
			}
			return true;
		}
	}
	return false;
}

function registraConta($username) {
	session_start();
	session_unset();
	$link = mysqli_connect("localhost", "root", "", "sistema");
	$sql = "SELECT id_account FROM account WHERE username = '".$username."'";
	$result = mysqli_query($link, $sql);
	if ($result) {
		if ($row = mysqli_fetch_assoc($result)) {
			$_SESSION["CONTA_ID"] = $row["id_account"];
		}
	}
}

function logout() {
	session_start();
	session_unset();
	session_destroy();
	header("Location: /sistema/admin/login.php");
	exit;
}

function validaSessao() {
	session_start();
	if (empty($_SESSION["CONTA_ID"])) {
		header("Location: /sistema/admin/login.php");
		exit;
	}
}

function validaUserProduto() {
	// Recupera o ID do usuário logado
	$default_id = isset($id_conta)
		? $id_conta
		: (isset($_SESSION['id'])
			? $_SESSION['id']
			: (isset($_SESSION['CONTA_ID']) ? $_SESSION['CONTA_ID'] : '')) ;

	// Verifica se existe ID
	if (!isset($_GET['id']) || empty($_GET['id'])) {
		echo "<script>alert('ID inválido.'); window.location.href = '/sistema/admin/prod/';</script>";
		exit;
	}

	$id_product = intval($_GET['id']);

	// Busca o produto para validar o dono
	$linkVal = mysqli_connect("localhost", "root", "", "sistema");

	$sql = "SELECT id_account FROM product WHERE id_product = $id_product LIMIT 1;";
	$result = mysqli_query($linkVal, $sql);

	if (!$result || mysqli_num_rows($result) == 0) {
		echo "<script>alert('Produto não encontrado.'); window.location.href = '/sistema/admin/prod/';</script>";
		exit;
	}

	$row = mysqli_fetch_assoc($result);

	// Verifica se o produto pertence ao usuário
	if ($row['id_account'] != $default_id) {
		echo "<script>alert('O usuário não tem permissão neste item.'); window.location.href = '/sistema/admin/prod/';</script>";
		exit;
	}
}
?>
