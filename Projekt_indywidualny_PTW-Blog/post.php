<?php
session_start();
$polaczenie = mysqli_connect('localhost', 'root', '', 'blog');
if (!$polaczenie) {
    die('Błąd połączenia z bazą danych');
}

$postId = (int)($_GET['id'] ?? 0);
$post = null;

$stmt = mysqli_prepare($polaczenie, "SELECT posty.*, uzytkownicy.nazwa_uzytkownika AS autor FROM posty JOIN uzytkownicy ON posty.id_uzytkownika = uzytkownicy.id_uzytkownika WHERE posty.id_posta = ?");
mysqli_stmt_bind_param($stmt, "i", $postId);
mysqli_stmt_execute($stmt);
$wynik = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($wynik);

if (isset($_POST['dodaj_komentarz'])) {
    if (!isset($_SESSION['id'])) {
        echo '<script>alert("Musisz być zalogowany, aby dodać komentarz."); window.location.href="login.php";</script>';
        exit();
    }

    $id_uzytkownika = (int)$_SESSION['id'];
    $id_posta = (int)($_POST['id_posta'] ?? 0);
    $tresc_komentarza = trim($_POST['tresc_komentarza'] ?? '');

    if ($id_posta > 0 && $tresc_komentarza !== '') {
        $stmt = mysqli_prepare($polaczenie, "INSERT INTO komentarze (id_uzytkownika, id_posta, tresc_komentarza) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iis", $id_uzytkownika, $id_posta, $tresc_komentarza);
        mysqli_stmt_execute($stmt);
    }

    header("Location: post.php?id=" . $id_posta);
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post</title>
    <link rel="stylesheet" href="styl.css?v=<?php echo time(); ?>">
</head>
<body>
    <div id="pasekgorny">
        <img id="logo" src="zdjecia/blogger.png" alt="Logo">
        <?php if (!isset($_SESSION['id'])): ?>
            <a id="index" href="index.php">Blog</a>
        <?php else: ?>
            <a id="index" href="welcome.php">Blog</a>
        <?php endif; ?>
    </div>

    <?php if ($post): ?>
        <?php
        $stmt = mysqli_prepare($polaczenie, "SELECT COUNT(*) AS likes FROM glosy WHERE id_posta = ? AND glosowanie = 1");
        mysqli_stmt_bind_param($stmt, "i", $postId);
        mysqli_stmt_execute($stmt);
        $likes = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['likes'];

        $stmt = mysqli_prepare($polaczenie, "SELECT COUNT(*) AS dislikes FROM glosy WHERE id_posta = ? AND glosowanie = -1");
        mysqli_stmt_bind_param($stmt, "i", $postId);
        mysqli_stmt_execute($stmt);
        $dislikes = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['dislikes'];

        $adres_kategorii = isset($_SESSION['id']) ? 'welcome.php' : 'index.php';
        ?>

        <div class="stronaoposcie">
            <img src="<?php echo htmlspecialchars($post['zdjecie']); ?>" alt="Zdjęcie posta">
            <h1><?php echo htmlspecialchars($post['tytul']); ?></h1>
            <h3>Autor: <?php echo htmlspecialchars($post['autor']); ?></h3>
            <h3>Kategoria: <a href="<?php echo $adres_kategorii; ?>?kategoria=<?php echo urlencode($post['kategoria']); ?>"><?php echo htmlspecialchars($post['kategoria']); ?></a></h3>
            <h3>Opublikowano: <?php echo htmlspecialchars($post['data_utworzenia']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($post['tresc'])); ?></p>
        </div>

        <div class="like-section">
            <button class="like-btn" data-id="<?php echo $postId; ?>">👍 Like (<span id="like-count"><?php echo $likes; ?></span>)</button>
            <button class="dislike-btn" data-id="<?php echo $postId; ?>">👎 Dislike (<span id="dislike-count"><?php echo $dislikes; ?></span>)</button>
        </div>

        <hr>
        <h2 class="dodaj_komentarz">Dodaj komentarz:</h2>
        <form method="post" action="post.php?id=<?php echo $postId; ?>">
            <input type="hidden" name="id_posta" value="<?php echo $postId; ?>">
            <textarea name="tresc_komentarza" id="tresc_komentarza" placeholder="Co o tym myślisz?" required></textarea><br>
            <button type="submit" name="dodaj_komentarz" id="dodanie_komentarza">Dodaj komentarz</button>
        </form>

        <hr>
        <div class="komentarze">
            <h2>Wszystkie komentarze:</h2>
            <?php
            $stmt = mysqli_prepare($polaczenie, "SELECT komentarze.tresc_komentarza, uzytkownicy.nazwa_uzytkownika FROM komentarze JOIN uzytkownicy ON komentarze.id_uzytkownika = uzytkownicy.id_uzytkownika WHERE komentarze.id_posta = ? ORDER BY komentarze.id_komentarza DESC");
            mysqli_stmt_bind_param($stmt, "i", $postId);
            mysqli_stmt_execute($stmt);
            $wynik_komentarze = mysqli_stmt_get_result($stmt);
            ?>

            <?php while ($komentarz = mysqli_fetch_assoc($wynik_komentarze)): ?>
                <div class="komentarz">
                    <p><b><?php echo htmlspecialchars($komentarz['nazwa_uzytkownika']); ?></b> napisał: <?php echo htmlspecialchars($komentarz['tresc_komentarza']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="brak">Nie znaleziono posta o podanym ID.</p>
    <?php endif; ?>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".like-btn, .dislike-btn").forEach(button => {
            button.addEventListener("click", function () {
                const postId = this.getAttribute("data-id");
                const action = this.classList.contains("like-btn") ? "like" : "dislike";

                fetch("like_dislike.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `postId=${postId}&action=${action}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    document.getElementById("like-count").innerText = data.likes;
                    document.getElementById("dislike-count").innerText = data.dislikes;
                });
            });
        });
    });
    </script>
</body>
</html>
