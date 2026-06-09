<?php
require_once "sesja.php";

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Witaj</title>
    <link rel="stylesheet" href="styl.css">
</head>
<body>
    <div class="settings-container">
    <h1>Cześć, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Witaj w ustawieniach strony.</h1>
    <p>
        <a href="reset-haslo.php" class="btn btn-warning">Zresetuj hasło</a>
        <a href="welcome.php" class="btn btn-primary">Wróć na stronę</a>
    </p>
    <h3>Oto Twoje posty:</h3>
    <div class="table-wrapper">
    <table class="posts-table">
        <tr>
            <th>Nazwa</th>
            <th>Treść</th>
            <th>Kategoria</th>
            <th>Data utworzenia</th>
            <th>Akcje</th>
        </tr>
        <?php
            $id_uzytkownika = $_SESSION["id"];
            $stmt = $polaczenie->prepare("SELECT id_posta, tytul, tresc, kategoria, data_utworzenia FROM posty WHERE id_uzytkownika = ?");
            $stmt->bind_param("i", $id_uzytkownika);
            $stmt->execute();
            $result = $stmt->get_result();

            $stmt2 = $polaczenie->prepare("SELECT komentarze.id_komentarza, komentarze.id_posta, komentarze.tresc_komentarza, posty.tytul AS tytul_posta FROM komentarze JOIN posty ON komentarze.id_posta = posty.id_posta WHERE komentarze.id_uzytkownika = ?");
            $stmt2->bind_param("i", $id_uzytkownika);
            $stmt2->execute();
            $result2 = $stmt2->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_array()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row[1]) . "</td>";
                    echo "<td>" . htmlspecialchars($row[2]) . "</td>";
                    echo "<td>" . htmlspecialchars($row[3]) . "</td>";
                    echo "<td>" . htmlspecialchars($row[4]) . "</td>";
                    echo "<td>
                            <a href='edytowaniepost.php?id=" . $row[0] . "' class='btn btn-warning'>Edytuj</a>
                            <a href='usuwaniepost.php?id=" . $row[0] . "' class='btn btn-danger' onclick='return confirm(\"Na pewno chcesz usunąć ten post?\")'>Usuń</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'><div class='alert alert-info'>Nie masz żadnych postów.</div></td></tr>";
            }

            $stmt->close();
        ?>
    </table>
    </div>
    <h3>Oto Twoje komentarze:</h3>
    <div class="table-wrapper">
    <table class="posts-table">
        <tr>
            <th>Post</th>
            <th>Treść komentarza</th>
            <th>Akcje</th>
        </tr>
        <?php if ($result2->num_rows > 0): ?>
            <?php while ($komentarz = $result2->fetch_assoc()): ?>
                <tr>
                    <td>
                        <a href="post.php?id=<?php echo (int)$komentarz['id_posta']; ?>">
                            <?php echo htmlspecialchars($komentarz['tytul_posta']); ?>
                        </a>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($komentarz['tresc_komentarza']); ?>
                    </td>

                    <td>
                        <a href="edytowaniekomentarza.php?id=<?php echo (int)$komentarz['id_komentarza']; ?>" class="btn btn-warning btn-sm">
                            Edytuj
                        </a>

                        <a href="usuwaniekomentarza.php?id=<?php echo (int)$komentarz['id_komentarza']; ?>" 
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('Na pewno chcesz usunąć ten komentarz?');">
                            Usuń
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">
                    <div class="alert alert-info">
                        Nie masz jeszcze żadnych komentarzy.
                    </div>
                </td>
            </tr>
        <?php endif; ?>
    </table>
    </div>
    <?php
        $stmt2->close();
        $polaczenie->close();
    ?>
    </div>
</body>
</html>
