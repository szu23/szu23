<?php
require_once "konfiguracja.php";

$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
 
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    if(empty(trim($_POST["username"]))){
        $username_err = "Podaj nazwę użytkownika.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Nazwa użytkownika może składać się tylko z liter, cyfr i pokreślników.";
    } else{
        $sql = "SELECT id_uzytkownika FROM uzytkownicy WHERE nazwa_uzytkownika = ?";
        
        if($stmt = $polaczenie->prepare($sql)){
            $stmt->bind_param("s", $param_username);

            $param_username = trim($_POST["username"]);

            if($stmt->execute()){

                $stmt->store_result();
                
                if($stmt->num_rows == 1){
                    $username_err = "Ta nazwa jest zajęta.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Spróbuj ponownie później.";
            }

            $stmt->close();
        }
    }
    
    $pattern = '/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{8,20}$/';

    if(empty(trim($_POST["password"]))){
        $password_err = "Podaj hasło.";     
    } elseif(strlen(trim($_POST["password"])) < 8){
        $password_err = "Hasło musi zawierać przynajmniej 8 znaków.";
    } elseif(!preg_match($pattern,$_POST["password"])){
        $password_err = "Hasło musi zawierać przynajmniej jedną wielką literę, cyfrę i znak specjalny.";
    } else{
        $password = trim($_POST["password"]);
    }

    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Podaj ponownie hasło.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Hasła nie są takie same.";
        }
    }

    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){

        $sql = "INSERT INTO uzytkownicy (nazwa_uzytkownika, haslo) VALUES (?, ?)";
         
        if($stmt = $polaczenie->prepare($sql)){

            $stmt->bind_param("ss", $param_username, $param_password);
   
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            
            if($stmt->execute()){
                header("location: login.php");
            } else{
                echo "Spróbuj ponownie później.";
            }

            $stmt->close();
        }
    }

    $polaczenie->close();
}
?>
 
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejestracja</title>
    <link rel="stylesheet" href="styl.css">
</head>
<body>
    <div id="pasekgorny">
        <img id="logo" src="zdjecia/blogger.png" alt="">
        <a id="index" href="index.php">Blog</a>
    </div>

    <div class="wrapper">
        <h2>Rejestracja konta</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Nazwa użytkownika</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Hasło</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Potwierdź hasło</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Potwierdź">
                <input type="reset" class="btn btn-secondary ml-2" value="Zresetuj">
            </div>
            <p>Masz konto? <a href="login.php">Zaloguj się tutaj</a>.</p>
        </form>
    </div>    
</body>
</html>