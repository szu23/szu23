<?php
require_once "sesja.php";

if (!isset($_GET["id"])) {
    header("Location: settings.php");
    exit();
}

$id_komentarza = (int)$_GET["id"];
$id_uzytkownika = (int)$_SESSION["id"];

$stmt = $polaczenie->prepare("DELETE FROM komentarze WHERE id_komentarza = ? AND id_uzytkownika = ?");

$stmt->bind_param("ii", $id_komentarza, $id_uzytkownika);
$stmt->execute();

header("Location: settings.php");
exit();
?>