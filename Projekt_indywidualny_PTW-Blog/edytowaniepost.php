<?php
require_once "sesja.php";

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_id = (int)$_POST["id"];
    $tytul = $_POST["tytul"];
    $tresc = $_POST["tresc"];
    $kategoria = $_POST["kategoria"];

    $stmt = $polaczenie->prepare("UPDATE posty SET tytul = ?, tresc = ?, kategoria = ? WHERE id_posta = ? AND id_uzytkownika = ?");
    $stmt->bind_param("sssii", $tytul, $tresc, $kategoria, $post_id, $_SESSION["id"]);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script>alert('Post został zaktualizowany'); window.location.href = 'settings.php';</script>";
    } else {
        echo "<script>alert('Wystąpił problem podczas aktualizacji. Spróbuj ponownie.');</script>";
    }

    $stmt->close();
} else {
    $post_id = (int)$_GET["id"];
    $stmt = $polaczenie->prepare("SELECT tytul, tresc, kategoria FROM posty WHERE id_posta = ? AND id_uzytkownika = ?");
    $stmt->bind_param("ii", $post_id, $_SESSION["id"]);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_array();
        $tytul = $row["tytul"];
        $tresc = $row["tresc"];
        $kategoria = $row["kategoria"];
    } else {
        echo "<script>alert('Post nie istnieje'); window.location.href = 'welcome.php';</script>";
        exit();
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj post</title>
    <link rel="stylesheet" href="styl.css">
</head>
<body>
    <div class="settings-container">
        <h2>Edytuj post</h2>
        <form action="edytowaniepost.php" method="post">
            <input type="hidden" name="id" value="<?php echo (int)$post_id; ?>">
            <div class="form-group">
                <label for="tytul">Tytuł:</label>
                <input type="text" class="form-control" id="tytul" name="tytul" value="<?php echo htmlspecialchars($tytul); ?>" required>
            </div>
            <div class="form-group">
                <label for="tresc">Treść:</label>
                <textarea class="form-control" id="tresc" name="tresc" rows="4" required><?php echo htmlspecialchars($tresc); ?></textarea>
            </div>
            <div class="form-group">
                <label for="kategoria">Kategoria:</label>
                <input type="text" class="form-control" id="kategoria" name="kategoria" value="<?php echo htmlspecialchars($kategoria); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Zaktualizuj post</button>
            <a href="settings.php" class="btn btn-secondary">Wróć do ustawień</a>
        </form>
    </div>
</body>
</html>
