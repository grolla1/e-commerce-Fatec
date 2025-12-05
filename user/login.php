<?php

include("./config.inc.php");
include("./session.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (contaValida($_POST["username"], $_POST["password"])) {
		registraConta($_POST["username"]);
		header("Location: /sistema/user/index.php");
		exit;
	}
	$username = $_POST["username"];
	$mensagem = "Username ou Password incorreto!";
}

include("../header.php");

?>

<div class="login-container">
    <form name="formLogin" method="POST" class="login-form">

        <h2>LOGIN</h2>

        <div class="error-message">
            <?=isset($mensagem)?$mensagem:"&nbsp;";?>
        </div>

        <div class="input-group">
            <label for="username">Usuário</label>
            <input type="text" id="username" name="username" value="<?=isset($username)?$username:"";?>" required>
        </div>

        <div class="input-group">
            <label for="password">Senha</label>
            <input type="password" id="password" name="password" value="<?=isset($password)?$password:"";?>" required>
        </div>

        <button type="submit" name="submit">Entrar</button>

        <div class="register-link">
            <p>Não tem uma conta? <a href="/sistema/user/register.php">Registre-se</a></p>
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
	//-->
</script>

<?php
include("../footer.php");
?>