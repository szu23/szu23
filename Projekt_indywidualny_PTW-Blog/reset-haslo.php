<?php
require_once "sesja.php";

$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
 

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $pattern = '/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{8,20}$/';
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Podaj nowe hasło.";     
    } elseif(strlen(trim($_POST["new_password"])) < 8){
        $new_password_err = "Hasło musi zawierać co najmniej 8 znaków.";
    } elseif(!preg_match($pattern,$_POST["new_password"])){
        $new_password_err = "Hasło musi zawierać przynajmniej jedną wielką literę, cyfrę i znak specjalny.";
    }else{
        $new_password = trim($_POST["new_password"]);
    }
    
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Potwierdź hasło.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Hasła nie są takie same.";
        }
    }
        
    if(empty($new_password_err) && empty($confirm_password_err)){

        $sql = "UPDATE uzytkownicy SET haslo = ? WHERE id_uzytkownika = ?";
        
        if($stmt = $polaczenie->prepare($sql)){

            $stmt->bind_param("si", $param_password, $param_id);
            
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];
            
            if($stmt->execute()){
                session_destroy();
                header("location: login.php");
                exit();
            } else{
                echo "Spróbuj ponownie później";
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
    <title>Resetowanie hasła</title>
    <link rel="stylesheet" href="styl.css">
</head>
<body>
    <div class="wrapper">
        <h2>Zresetuj hasło</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
            <div class="form-group">
                <label>Nowe hasło</label>
                <input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password; ?>">
                <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Potwierdź hasło</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Potwierdź">
                <a class="btn btn-link ml-2" href="settings.php">Anuluj</a>
            </div>
        </form>
    </div>    
</body>
</html>
