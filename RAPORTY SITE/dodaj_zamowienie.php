<?php
$conn = new mysqli("localhost", "user", "pass", "raporty");

// Dodaj zamówienie
$stmt = $conn->prepare("INSERT INTO zamowienia (ID_Klienta, Data_Zamowienia, Status) VALUES (?, NOW(), 'Nowe')");
$stmt->bind_param("i", $_POST['id_klienta']);
$stmt->execute();
$orderId = $conn->insert_id;

// Dodaj produkty do zamówienia
$stmt = $conn->prepare("INSERT INTO zamowienia_produkty (ID_Zamowienia, ID_Prodkutu, Ilosc) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $orderId, $_POST['id_produktu'], $_POST['ilosc']);
$stmt->execute();

echo "Zamówienie nr $orderId zostało dodane!";
?>