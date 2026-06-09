<?php
require_once "sesja.php";

$tresc = "";
$tresc_err = "";
$id_posta = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_komentarza = (int)$_POST["id_komentarza"];
    $id_uzytkownika = (int)$_SESSION["id"];
    $tresc = trim($_POST["tresc_komentarza"]);

    if ($tresc == "") {
        $tresc_err = "Treść komentarza nie może być pusta.";
    }

    if ($tresc_err == "") {
        $stmt = $polaczenie->prepare("
            UPDATE komentarze 
            SET tresc_komentarza = ? 
            WHERE id_komentarza = ? 
            AND id_uzytkownika = ?
        ");

        $stmt->bind_param("sii", $tresc, $id_komentarza, $id_uzytkownika);

        if ($stmt->execute()) {
            header("Location: settings.php");
            exit();
        } else {
            echo "Błąd podczas edycji komentarza.";
        }
    }
} else {
    if (!isset($_GET["id"])) {
        header("Location: settings.php");
        exit();
    }

    $id_komentarza = (int)$_GET["id"];
    $id_uzytkownika = (int)$_SESSION["id"];

    $stmt = $polaczenie->prepare("
        SELECT id_komentarza, id_posta, tresc_komentarza 
        FROM komentarze 
        WHERE id_komentarza = ? 
        AND id_uzytkownika = ?
    ");

    $stmt->bind_param("ii", $id_komentarza, $id_uzytkownika);
    $stmt->execute();

    $wynik = $stmt->get_result();

    if ($wynik->num_rows != 1) {
        echo "Nie znaleziono komentarza albo nie masz uprawnień do jego edycji.";
        exit();
    }

    $komentarz = $wynik->fetch_assoc();

    $id_komentarza = $komentarz["id_komentarza"];
    $id_posta = $komentarz["id_posta"];
    $tresc = $komentarz["tresc_komentarza"];
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Edytowanie komentarza</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="styl.css">
</head>
<body>

<div class="settings-container">
    <h1>Edytuj komentarz</h1>

    <form method="post" action="edytowaniekomentarza.php">
        <input type="hidden" name="id_komentarza" value="<?php echo (int)$id_komentarza; ?>">

        <div class="mb-3">
            <label class="form-label">Treść komentarza:</label>
            <textarea name="tresc_komentarza" class="form-control" rows="6" required><?php echo htmlspecialchars($tresc); ?></textarea>

            <?php if ($tresc_err != ""): ?>
                <div class="text-danger">
                    <?php echo $tresc_err; ?>
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-warning">Zapisz zmiany</button>

        <a href="settings.php" class="btn btn-primary">
            Anuluj
        </a>
    </form>
</div>

</body>
</html>