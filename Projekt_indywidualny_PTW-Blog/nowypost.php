<?php
require_once "sesja.php";

$kategorie2 = mysqli_query($polaczenie, "SELECT nazwa_kategorii FROM kategorie ORDER BY nazwa_kategorii");
$kategorie3 = mysqli_fetch_all($kategorie2, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj post</title>
    <link rel="stylesheet" href="styl.css?v=<?php echo time(); ?>">
</head>
<body>
    <div id="pasekgorny">
        <img id="logo" src="zdjecia/blogger.png" alt="Logo">
        <a id="index" href="welcome.php">Blog</a>
    </div>

    <div class="formpost">
        <form method="post" action="nowypost2.php" enctype="multipart/form-data">
            <label for="tytulpostu">Tytuł postu:</label>
            <input type="text" name="tytulpostu" id="tytulpostu" required><br><br>

            <label for="trescpostu">Treść postu:</label>
            <textarea name="trescpostu" id="trescpostu" required></textarea><br><br>

            <label for="kategoriapostu">Kategoria postu:</label>
            <select name="kategoriapostu" id="kategoriapostu" required>
                <?php foreach ($kategorie3 as $kategoria): ?>
                    <option value="<?php echo htmlspecialchars($kategoria['nazwa_kategorii']); ?>">
                        <?php echo htmlspecialchars($kategoria['nazwa_kategorii']); ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <label for="zdjeciepostu">Zdjęcie:</label>
            <input type="file" name="zdjeciepostu" id="zdjeciepostu" accept="image/*" required><br>

            <button id="dodajpost2" type="submit">Dodaj post</button>
        </form>
    </div>
</body>
</html>
