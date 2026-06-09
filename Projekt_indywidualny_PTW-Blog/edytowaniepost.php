<?php
require_once "sesja.php";

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

$tytul = "";
$tresc = "";
$kategoria = "";
$post_id = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_id = (int)($_POST["id"] ?? 0);
    $tytul = trim($_POST["tytul"] ?? "");
    $tresc = trim($_POST["tresc"] ?? "");
    $kategoria = trim($_POST["kategoriapostu"] ?? "");

    if ($post_id <= 0 || $tytul == "" || $tresc == "" || $kategoria == "") {
        echo "<script>alert('Uzupełnij wszystkie pola.'); window.location.href = 'settings.php';</script>";
        exit();
    }

    $stmt = $polaczenie->prepare("UPDATE posty SET tytul = ?, tresc = ?, kategoria = ? WHERE id_posta = ? AND id_uzytkownika = ?");
    $stmt->bind_param("sssii", $tytul, $tresc, $kategoria, $post_id, $_SESSION["id"]);
    $stmt->execute();

    if ($stmt->affected_rows >= 0) {
        echo "<script>alert('Post został zaktualizowany'); window.location.href = 'settings.php';</script>";
        exit();
    } else {
        echo "<script>alert('Wystąpił problem podczas aktualizacji. Spróbuj ponownie.'); window.location.href = 'settings.php';</script>";
        exit();
    }

    $stmt->close();
} else {
    if (!isset($_GET["id"])) {
        header("Location: settings.php");
        exit();
    }

    $post_id = (int)$_GET["id"];

    if ($post_id <= 0) {
        header("Location: settings.php");
        exit();
    }

    $stmt = $polaczenie->prepare("SELECT tytul, tresc, kategoria FROM posty WHERE id_posta = ? AND id_uzytkownika = ?");
    $stmt->bind_param("ii", $post_id, $_SESSION["id"]);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        $tytul = $row["tytul"];
        $tresc = $row["tresc"];
        $kategoria = $row["kategoria"];
    } else {
        echo "<script>alert('Post nie istnieje albo nie masz uprawnień do jego edycji.'); window.location.href = 'settings.php';</script>";
        exit();
    }

    $stmt->close();
}

$kategorie2 = mysqli_query($polaczenie, "SELECT nazwa_kategorii FROM kategorie ORDER BY nazwa_kategorii");
$kategorie3 = mysqli_fetch_all($kategorie2, MYSQLI_ASSOC);
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
                <textarea class="form-control" id="tresc" name="tresc" rows="5" required><?php echo htmlspecialchars($tresc); ?></textarea>
            </div>

            <div class="form-group">
                <label for="kategoriapostu">Kategoria postu:</label>

                <select name="kategoriapostu" id="kategoriapostu" class="form-control" required>
                    <?php foreach ($kategorie3 as $kat): ?>
                        <option value="<?php echo htmlspecialchars($kat['nazwa_kategorii']); ?>"<?php if ($kat['nazwa_kategorii'] == $kategoria) echo "selected"; ?>>
                            <?php echo htmlspecialchars($kat['nazwa_kategorii']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                Zaktualizuj post
            </button>

            <a href="settings.php" class="btn btn-secondary">
                Wróć do ustawień
            </a>
        </form>
    </div>
</body>
</html>