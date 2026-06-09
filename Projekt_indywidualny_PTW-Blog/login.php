<?php
session_start();
 
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true){
    header("location: welcome.php");
    exit;
}

require_once "konfiguracja.php";
 
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    if(empty(trim($_POST["username"]))){
        $username_err = "Podaj nazwę użytkownika.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    if(empty(trim($_POST["password"]))){
        $password_err = "Podaj hasło.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT id_uzytkownika, nazwa_uzytkownika, haslo FROM uzytkownicy WHERE nazwa_uzytkownika = ?";
        
        if($stmt = $polaczenie->prepare($sql)){
            $stmt->bind_param("s", $param_username);
            
            $param_username = $username;
            
            if($stmt->execute()){
                $stmt->store_result();
                
                if($stmt->num_rows == 1){                    
                    $stmt->bind_result($id, $username, $hashed_password);
                    if($stmt->fetch()){
                        if(password_verify($password, $hashed_password)){
                            session_start();
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            header("location: welcome.php");
                        } else{
                            $login_err = "Nieprawidłowa nazwa użytkownika lub hasło.";
                        }
                    }
                } else{
                    $login_err = "Nieprawidłowa nazwa użytkownika lub hasło.";
                }
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
    <title>Logowanie</title>
    <link rel="stylesheet" href="styl.css">
</head>
<body>

    <div id="pasekgorny">
        <img id="logo" src="zdjecia/blogger.png" alt="">
        <a id="index" href="index.php">Blog</a>
    </div>

    <div class="wrapper">
        <h2>Logowanie</h2>
        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Nazwa użytkownika</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Hasło</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Zaloguj">
            </div>
            <p>Nie masz konta? <a href="rejestracja.php">Zarejestruj się tutaj</a>.</p>
        </form>
    </div>
</body>
</html>
