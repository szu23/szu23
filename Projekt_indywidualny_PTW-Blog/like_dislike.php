<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
$polaczenie = mysqli_connect('localhost', 'root', '', 'blog');

if (!isset($_SESSION['id'])) {
    echo json_encode(['error' => 'Musisz być zalogowany, aby głosować.']);
    exit();
}

$id_uzytkownika = (int)$_SESSION['id'];
$postId = (int)($_POST['postId'] ?? 0);
$action = $_POST['action'] ?? '';

if ($postId <= 0 || !in_array($action, ['like', 'dislike'])) {
    echo json_encode(['error' => 'Nieprawidłowe dane.']);
    exit();
}

$glosowanie = ($action == 'like') ? 1 : -1;

$stmt = $polaczenie->prepare("SELECT glosowanie FROM glosy WHERE id_uzytkownika = ? AND id_posta = ?");
$stmt->bind_param("ii", $id_uzytkownika, $postId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt = $polaczenie->prepare("UPDATE glosy SET glosowanie = ? WHERE id_uzytkownika = ? AND id_posta = ?");
    $stmt->bind_param("iii", $glosowanie, $id_uzytkownika, $postId);
    $stmt->execute();
} else {
    $stmt = $polaczenie->prepare("INSERT INTO glosy (id_uzytkownika, id_posta, glosowanie) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $id_uzytkownika, $postId, $glosowanie);
    $stmt->execute();
}

$stmt = $polaczenie->prepare("SELECT COUNT(*) as likes FROM glosy WHERE id_posta = ? AND glosowanie = 1");
$stmt->bind_param("i", $postId);
$stmt->execute();
$likes = $stmt->get_result()->fetch_assoc()['likes'];

$stmt = $polaczenie->prepare("SELECT COUNT(*) as dislikes FROM glosy WHERE id_posta = ? AND glosowanie = -1");
$stmt->bind_param("i", $postId);
$stmt->execute();
$dislikes = $stmt->get_result()->fetch_assoc()['dislikes'];

echo json_encode(['likes' => $likes, 'dislikes' => $dislikes]);
?>
