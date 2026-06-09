<?php
require_once "sesja.php";

$kategorie2 = mysqli_query($polaczenie, "SELECT nazwa_kategorii FROM kategorie ORDER BY nazwa_kategorii");
$kategorie3 = mysqli_fetch_all($kategorie2, MYSQLI_ASSOC);

$wybranakategoria = $_GET['kategoria'] ?? '';
$fraza = trim($_GET['fraza'] ?? '');

$sql = "SELECT * FROM posty WHERE 1";
$typy = "";
$parametry = [];

if ($wybranakategoria !== '') {
    $sql .= " AND kategoria = ?";
    $typy .= "s";
    $parametry[] = $wybranakategoria;
}

if ($fraza !== '') {
    $sql .= " AND (tytul LIKE ? OR tresc LIKE ?)";
    $typy .= "ss";
    $szukaj = "%" . $fraza . "%";
    $parametry[] = $szukaj;
    $parametry[] = $szukaj;
}

$sql .= " ORDER BY data_utworzenia DESC, id_posta DESC";
$stmt = mysqli_prepare($polaczenie, $sql);
if ($typy !== '') {
    mysqli_stmt_bind_param($stmt, $typy, ...$parametry);
}
mysqli_stmt_execute($stmt);
$posty2 = mysqli_stmt_get_result($stmt);
$posty3 = mysqli_fetch_all($posty2, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona główna z postami</title>
    <link rel="stylesheet" href="styl.css?v=<?php echo time(); ?>">
</head>
<body>
    <div id="pasekgorny">
        <img id="logo" src="zdjecia/blogger.png" alt="Logo">
        <a id="index" href="welcome.php">Blog</a>
        <a href="logout.php" id="logout">Wyloguj się</a>
        <a href="settings.php"><img src="zdjecia/settings.png" id="settings_icon" alt="settings"></a>
    </div>

    <div id="container">
        <div id="pasekboczny">
            <h2 class="h2onamainmenu">Kategorie:</h2>
            <?php foreach ($kategorie3 as $kategoria): ?>
                <a href="welcome.php?kategoria=<?php echo urlencode($kategoria['nazwa_kategorii']); ?>">
                    <?php echo htmlspecialchars($kategoria['nazwa_kategorii']); ?>
                </a><br>
            <?php endforeach; ?>
            <br>
            <a id="reset" href="welcome.php">Wyczyść</a>
        </div>

        <div id="posty">
            <h1>Witaj <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>, zobacz naszą stronę</h1>

            <form method="get" action="nowypost.php"><br>
                <input id="dodajpost" type="submit" value="Dodaj nowy post">
            </form>

            <form class="wyszukiwarka" method="get" action="welcome.php">
                <?php if ($wybranakategoria !== ''): ?>
                    <input type="hidden" name="kategoria" value="<?php echo htmlspecialchars($wybranakategoria); ?>">
                <?php endif; ?>
                <input type="text" name="fraza" placeholder="Szukaj po tytule lub treści" value="<?php echo htmlspecialchars($fraza); ?>">
                <button type="submit">Szukaj</button>
            </form>

            <?php if ($wybranakategoria !== ''): ?>
                <h2 class="napisdlapostow">Posty z kategorii: <?php echo htmlspecialchars($wybranakategoria); ?></h2>
            <?php else: ?>
                <h2 class="napisdlapostow">Wszystkie posty:</h2>
            <?php endif; ?>

            <ul>
                <?php foreach ($posty3 as $post): ?>
                    <?php $czescopisu = substr($post['tresc'], 0, 70) . '...'; ?>
                    <li>
                        <img src="<?php echo htmlspecialchars($post['zdjecie']); ?>" alt="Zdjęcie posta">
                        <br><br>
                        <?php echo htmlspecialchars($post['tytul']); ?>
                        <p><?php echo htmlspecialchars($czescopisu); ?></p>
                        <a href="post.php?id=<?php echo (int)$post['id_posta']; ?>">Zobacz więcej</a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if (count($posty3) == 0): ?>
                <p class="brak">Brak postów spełniających podane kryteria.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
