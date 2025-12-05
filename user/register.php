<?php

include("./config.inc.php");
include("./session.php");
include("../header.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$link = mysqli_connect("localhost", "root", "", "sistema");
	$sql = "INSERT INTO account (name, email, phone, username, password, admin, active, seller, id_address) 
		values ('" . $_POST["name"] . "', '" . $_POST["email"] . "', '" . $_POST["phone"] . "', '" . $_POST["username"] . "', PASSWORD('" . $_POST["password"] . "', 'Y', 'Y', '1'))"; //cod 1 para adm
	try {
		// Primeiro, insere o endereço
		$address_sql = "INSERT INTO address (street, number, city, state, zip_code, country) VALUES (
			'" . $_POST["street"] . "',
			'" . $_POST["number"] . "',
			'" . $_POST["city"] . "',
			'" . $_POST["state"] . "',
			'" . $_POST["zip_code"] . "',
			'" . $_POST["country"] . "'
		)";
		$address_result = mysqli_query($link, $address_sql);
		if (!$address_result) {
			throw new Exception("Erro ao registrar endereço.");
		}
		$id_address = mysqli_insert_id($link);

		// Agora insere a conta usando o id_address
		$sql = "INSERT INTO account (name, email, phone, username, password, admin, active, seller, id_address) 
			VALUES (
				'" . $_POST["name"] . "',
				'" . $_POST["email"] . "',
				'" . $_POST["phone"] . "',
				'" . $_POST["username"] . "',
				PASSWORD('" . $_POST["password"] . "'),
				'N',
				'Y',
				'N',
				'" . $id_address . "'
			)";
		$result = mysqli_query($link, $sql);
		if ($result) {
			if (contaValida($_POST["username"], $_POST["password"])) {
				registraConta($_POST["username"]);
				header("Location: /sistema/user/index.php");
				exit;
			}
		}
		$username = $_POST["username"];
		$mensagem = "Username ou Password incorreto!";
	} catch (Exception $e) {
		$mensagem = "Erro ao registrar conta! Tente outro username.";
?>
		<div class="aviso" role="alert" aria-live="assertive" style="font-weight: bold; text-align: center;">
			Se o erro persistir, contate o administrador do sistema.
		</div>
<?php
	}
}

?>

<h3>REGISTRAR</h3>

<div class="login-container">

	<form name="formLogin" method="POST" class="login-form">

		<td colspan="2" style="color: red;">
			<?= isset($mensagem) ? $mensagem : "&nbsp;"; ?>
		</td>
		
		<div class="dados-pessoais">
			<div class="input-group">
				<label for="name">Nome:</label>
				<input type="text" name="name" value="<?= isset($_POST["name"]) ? htmlspecialchars($_POST["name"]) : ""; ?>">
			</div>
			<div class="input-group">
				<label for="email">Email:</label>
				<input type="email" name="email" value="<?= isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : ""; ?>">
			</div>
			<div class="input-group">
				<label for="phone">Telefone:</label>
				<input type="text" name="phone" value="<?= isset($_POST["phone"]) ? htmlspecialchars($_POST["phone"]) : ""; ?>">
			</div>
			<div class="input-group">
				<label for="username">Username:</label>
				<input type="text" name="username" value="<?= isset($username) ? htmlspecialchars($username) : ""; ?>">
			</div>
			<div class="input-group">
				<label for="password">Password:</label>
				<input type="password" name="password" value="<?= isset($password) ? htmlspecialchars($password) : ""; ?>">
			</div>
		</div>
		<div class="dados-endereco">

			<div class="input-group">
				<label for="zip_code">CEP:</label>
				<input type="text" name="zip_code" value="<?= isset($_POST["zip_code"]) ? htmlspecialchars($_POST["zip_code"]) : ""; ?>">
			</div>
			<div class="input-group">
				<label for="street">Rua:</label>
				<input type="text" name="street" value="<?= isset($_POST["street"]) ? htmlspecialchars($_POST["street"]) : ""; ?>">
			</div>
			<div class="input-group">
				<label for="number">Número:</label>
				<input type="text" name="number" value="<?= isset($_POST["number"]) ? htmlspecialchars($_POST["number"]) : ""; ?>">
			</div>
			<div class="input-group">
				<label for="city">Cidade:</label>
				<input type="text" name="city" value="<?= isset($_POST["city"]) ? htmlspecialchars($_POST["city"]) : ""; ?>">
			</div>
			<div class="input-group">
				<label for="state">Estado:</label>
				<input type="text" name="state" value="<?= isset($_POST["state"]) ? htmlspecialchars($_POST["state"]) : ""; ?>">
			</div>
			<div class="input-group">
				<label for="country">País:</label>
				<input type="text" name="country" value="<?= isset($_POST["country"]) ? htmlspecialchars($_POST["country"]) : ""; ?>">
			</div>
		</div>
		<div class="input-group">
			<input type="submit" name="submit" value="Submit">
		</div>

	</form>
</div>

<script language="JavaScript" type="text/javascript">
	<!--
	if (document.formLogin.username.value) {
		document.formLogin.password.focus();
	} else {
		document.formLogin.username.focus();
	}
	//
	-->
</script>

<?php
include("../footer.php");
?>