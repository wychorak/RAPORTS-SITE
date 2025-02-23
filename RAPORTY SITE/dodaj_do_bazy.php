<?php
// Dodaj_do_bazy.php
// Połączenie z bazą danych
$servername = "serwer2448789.home.pl";
$username = "38270794_wychor";
$password = "2T7STsFp*jjtz";
$dbname = "38270794_wychor";
$port = "3380";

$mysqli = new mysqli($servername, $username, $password, $dbname, $port);

// Sprawdź połączenie
if ($mysqli->connect_error) {
    die("Błąd połączenia: " . $mysqli->connect_error);
}

// Obsługa formularza klienta
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['client'])) {
    $stmt = $mysqli->prepare("INSERT INTO klienci (Imie, Nazwisko, Email, Telefon, Adres) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $_POST['imie'], $_POST['nazwisko'], $_POST['email'], $_POST['telefon'], $_POST['adres']);
    $stmt->execute();
}

// Obsługa formularza produktu
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product'])) {
    $stmt = $mysqli->prepare("INSERT INTO produkty (Nazwa, Cena, Opis, Dostepnosc, Cena_Retail) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdsdd", $_POST['nazwa'], $_POST['cena'], $_POST['opis'], $_POST['dostepnosc'], $_POST['cena_retail']);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj do bazy</title>
    <link rel="stylesheet" href="../css/style_dodaj_do_bazy.css">
</head>
<body>
    <a href="main.html" class="return-button">← Powrót</a>
    
    <div class="form-container">
        <!-- Formularz klienta -->
        <form method="POST" class="form-box">
            <h2>Nowy Klient</h2>
            <div class="form-group">
                <input type="text" name="imie" placeholder="Imię" required>
            </div>
            <div class="form-group">
                <input type="text" name="nazwisko" placeholder="Nazwisko" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="tel" name="telefon" placeholder="Telefon" required>
            </div>
            <div class="form-group">
                <input type="text" name="adres" placeholder="Adres" required>
            </div>
            <button type="submit" name="client">Zapisz Klienta</button>
        </form>

        <!-- Formularz produktu -->
        <form method="POST" class="form-box">
            <h2>Nowy Produkt</h2>
            <div class="form-group">
                <input type="text" name="nazwa" placeholder="Nazwa produktu" required>
            </div>
            <div class="form-group">
                <input type="number" step="0.01" name="cena" placeholder="Cena hurtowa" required>
            </div>
            <div class="form-group">
                <input type="number" step="0.01" name="cena_retail" placeholder="Cena detaliczna" required>
            </div>
            <div class="form-group">
                <textarea name="opis" placeholder="Opis produktu" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <select name="dostepnosc" required>
                    <option value="1">Dostępny</option>
                    <option value="0">Niedostępny</option>
                </select>
            </div>
            <button type="submit" name="product">Zapisz Produkt</button>
        </form>
    </div>
</body>
</html>