<?php
require_once "sesja.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: nowypost.php");
    exit();
}

$tytulpostu = trim($_POST["tytulpostu"] ?? "");
$trescpostu = trim($_POST["trescpostu"] ?? "");
$kategoriapostu = trim($_POST["kategoriapostu"] ?? "");
$id_uzytkownika = (int)$_SESSION["id"];

if ($tytulpostu == "" || $trescpostu == "" || $kategoriapostu == "" || !isset($_FILES["zdjeciepostu"])) {
    die("BŁĄD: Proszę wypełnić wszystkie pola. <a href='nowypost.php'>Wróć</a>");
}

if ($_FILES["zdjeciepostu"]["error"] != UPLOAD_ERR_OK) {
    die("BŁĄD: Nie udało się przesłać zdjęcia. <a href='nowypost.php'>Wróć</a>");
}

$dozwolone = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$rozszerzenie = strtolower(pathinfo($_FILES["zdjeciepostu"]["name"], PATHINFO_EXTENSION));

if (!in_array($rozszerzenie, $dozwolone)) {
    die("BŁĄD: Dozwolone formaty zdjęć: jpg, jpeg, png, gif, webp. <a href='nowypost.php'>Wróć</a>");
}

if (!is_dir("uploads")) {
    mkdir("uploads", 0777, true);
}

$nazwa_pliku = "uploads/" . time() . "_" . uniqid() . "." . $rozszerzenie;

if (!move_uploaded_file($_FILES["zdjeciepostu"]["tmp_name"], $nazwa_pliku)) {
    die("BŁĄD: Nie udało się zapisać zdjęcia. <a href='nowypost.php'>Wróć</a>");
}

$stmt = $polaczenie->prepare("INSERT INTO posty (id_uzytkownika, tytul, tresc, kategoria, zdjecie) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $id_uzytkownika, $tytulpostu, $trescpostu, $kategoriapostu, $nazwa_pliku);

if ($stmt->execute()) {
    header("Location: welcome.php");
    exit();
} else {
    echo "BŁĄD: " . htmlspecialchars($polaczenie->error);
}
?>
