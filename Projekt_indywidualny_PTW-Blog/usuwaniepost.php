<?php
require_once "sesja.php";

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET["id"])) {
    $post_id = (int)$_GET["id"];

    $stmt = $polaczenie->prepare("DELETE FROM komentarze WHERE id_posta = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $polaczenie->prepare("DELETE FROM glosy WHERE id_posta = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $polaczenie->prepare("DELETE FROM posty WHERE id_posta = ? AND id_uzytkownika = ?");
    $stmt->bind_param("ii", $post_id, $_SESSION["id"]);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script>alert('Post został usunięty'); window.location.href = 'settings.php';</script>";
    } else {
        echo "<script>alert('Wystąpił problem podczas usuwania. Spróbuj ponownie.'); window.location.href = 'settings.php';</script>";
    }

    $stmt->close();
} else {
    header("Location: welcome.php");
    exit();
}
