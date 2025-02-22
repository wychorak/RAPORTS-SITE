<?php
$servername = "localhost";
$username = "root"; // Zmień na prawdziwe dane
$password = ""; // Zmień na prawdziwe hasło
$dbname = "raporty";

// Utwórz połączenie
$conn = new mysqli($servername, $username, $password, $dbname);

// Sprawdź połączenie
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

// Przygotuj i wykonaj zapytanie
$stmt = $conn->prepare("INSERT INTO klienci (Imie, Nazwisko, Email, Telefon, Adres) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $_POST['imie'], $_POST['nazwisko'], $_POST['email'], $_POST['telefon'], $_POST['adres']);

if ($stmt->execute()) {
    echo "Klient został pomyślnie dodany!";
} else {
    echo "Błąd: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>