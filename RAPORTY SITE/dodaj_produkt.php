<?php
$servername = "localhost";
$username = "root"; // Zmień na prawdziwe dane
$password = ""; // Zmień na prawdziwe hasło
$dbname = "raporty";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

$stmt = $conn->prepare("INSERT INTO produkty (Nazwa, Cena, Opis, Dostepnosc, Cena_Retail) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sdsdd", $_POST['nazwa'], $_POST['cena'], $_POST['opis'], $_POST['dostepnosc'], $_POST['cena_retail']);

if ($stmt->execute()) {
    echo "Produkt został pomyślnie dodany!";
} else {
    echo "Błąd: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>