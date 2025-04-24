<?php
//inicializa sessão
session_start();



if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // Redireciona para a página apropriada com base no papel do usuário
    switch ($_SESSION["funcao"]) {
        case 1:
            header("location: garcom.php");
            break;
        case 2:
            header("location: recepcionista.php");
            break;
        case 3:
            header("location: gerente.php");
            break;
        default:
            header("location: index.php"); // Redireciona para a página de login por padrão
            break;
    }
    exit;
}

// Include config file
require_once "config.php";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if email is empty
    if (empty(trim($_POST["email"]))) {
        $email_err = "Por favor, insira o e-mail.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor, insira a senha.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($email_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT id, email, senha, funcao FROM employees WHERE email = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);

            // Set parameters
            $param_email = $email;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if email exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $db_email, $db_senha, $funcao);
                    if (mysqli_stmt_fetch($stmt)) {
                        //Verifica a senha diretamente.
                        if (password_verify($password, $db_senha)) {
                            // Password is correct, so start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $db_email;
                            $_SESSION["funcao"] = $funcao;

                            // Redirect user to the appropriate page based on role
                            switch ($funcao) {
                                case 1:
                                    header("location: garcom.php");
                                    break;
                                case 2:
                                    header("location: recepcionista.php");
                                    break;
                                case 3:
                                    header("location: gerente.php");
                                    break;
                                default:
                                    header("location: index.php"); // Redireciona para a página de login por padrão
                                    break;
                            }
                            exit;
                        } else {
                            // Password is not valid, display a generic error message
                            $login_err = "E-mail ou senha inválidos.";
                        }
                    }
                } else {
                    // email doesn't exist, display a generic error message
                    $login_err = "E-mail ou senha inválidos.";
                }
            } else {
                echo "Oops! Algo deu errado. Tente novamente mais tarde.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | MesaPro</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>
    <header>
        <div class="menubar">
            <div>
                <h1>Restaurante X</h1>
            </div>
        </div>
    </header>
    <main>
        <div class="container">
            <h2>Login</h2>
            <?php
            if (!empty($login_err)) {
                echo '<p style="color:red;">' . $login_err . '</p>';
            }
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="loginForm">
                <label for="user">E-mail:</label>
                <input type="text" id="email" name="email" placeholder="Digite seu e-mail" required>
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" placeholder="Digite sua senha" required>
                <button type="submit">Entrar</button>
            </form>
            <div class="forgot-password">
                <a href="#">Esqueceu a senha?</a>
            </div>
        </div>
    </main>
    <footer>
        <div class="footer-info">
            <p>&copy; 2025 Restaurante X. Todos os direitos reservados.</p>
            <p><a href="#">Contato</a> | <a href="#">Sobre</a></p>
        </div>
    </footer>
</body>

</html>