<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "raporty");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $zamowienie = $conn->query("
        SELECT 
            z.ID_Zamowienia AS zamowienie_id,
            z.Data_Zamowienia AS data_zlozenia,
            k.Imie, k.Nazwisko, k.Email, k.Adres AS klient_adres,
            SUM(p.Cena * zp.Ilosc) AS cena_zamowienia,
            SUM((p.Cena - p.Cena_Retail) * zp.Ilosc) AS profit_zamowienia
        FROM Zamowienia z
        JOIN Klienci k ON z.ID_Klienta = k.ID_Klienta
        JOIN Zamowienia_Produkty zp ON z.ID_Zamowienia = zp.ID_Zamowienia
        JOIN Produkty p ON zp.ID_Produktu = p.ID_Produktu
        WHERE z.ID_Zamowienia = $id
        GROUP BY z.ID_Zamowienia
    ")->fetch_assoc();

    if (!$zamowienie) die("Brak zamówienia o podanym ID.");

    $produkty = $conn->query("
        SELECT 
            p.ID_Produktu AS produkt_id,
            p.Nazwa AS nazwa_produktu,
            zp.Ilosc AS ilosc,
            p.Cena_Retail AS cena_zakupu,
            p.Cena AS cena_sprzedazy
        FROM Zamowienia_Produkty zp
        JOIN Produkty p ON zp.ID_Produktu = p.ID_Produktu
        WHERE zp.ID_Zamowienia = $id
    ");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Raport zamówienia</title>
    <link rel="stylesheet" href="../css/style_pobierz.css">
</head>
<body>
<div class="raport-content">
    <header>
        <h1>WychorskiProjectCompany</h1>
        <p>Adres: <?= htmlspecialchars($zamowienie['klient_adres'] ?? 'Brak adresu') ?></p>
        <p>Tel.: 123-456-789 | E-mail: biuro@wpc.com</p>
        <p>Data wygenerowania raportu: <?= date('d-m-Y') ?></p>
    </header>
    <h2>RAPORT ZAMÓWIENIA</h2>
    
    <section class="dane-podstawowe">
        <h3>Dane podstawowe</h3>
        <div class="grid">
            <span>Zamówienie ID</span><span><?= $zamowienie['zamowienie_id'] ?></span>
            <span>Data złożenia</span><span><?= date('d-m-Y H:i', strtotime($zamowienie['data_zlozenia'])) ?></span>
            <span>Klient</span><span><?= htmlspecialchars($zamowienie['Imie'] . ' ' . $zamowienie['Nazwisko']) ?></span>
            <span>Email klienta</span><span><?= htmlspecialchars($zamowienie['Email'] ?? 'Brak emaila') ?></span>
        </div>
    </section>
    <section class="szczegoly-finansowe">
        <h3>Szczegóły finansowe</h3>
        <div class="grid">
            <span>Koszt zamówienia (PLN)</span><span><?= number_format($zamowienie['cena_zamowienia'], 2) ?></span>
            <span>Profit sprzedaży (PLN)</span><span><?= number_format($zamowienie['profit_zamowienia'], 2) ?></span>
        </div>
    </section>
    <section class="lista-produktow">
        <h3>Lista produktów</h3>
        <table>
            <tr>
                <th>Produkt ID</th>
                <th>Nazwa produktu</th>
                <th>Ilość</th>
                <th>Cena zakupu (PLN)</th>
                <th>Cena sprzedaży (PLN)</th>
                <th>Profit (PLN)</th>
            </tr>
            <?php while ($prod = $produkty->fetch_assoc()): ?>
            <tr>
                <td><?= $prod['produkt_id'] ?></td>
                <td><?= htmlspecialchars($prod['nazwa_produktu']) ?></td>
                <td><?= $prod['ilosc'] ?></td>
                <td><?= number_format($prod['cena_zakupu'], 2) ?></td>
                <td><?= number_format($prod['cena_sprzedazy'], 2) ?></td>
                <td><?= number_format(($prod['cena_sprzedazy'] - $prod['cena_zakupu']) * $prod['ilosc'], 2) ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </section>
    <section class="notatki">
        <h3>Notatki dodatkowe</h3>
        <p>[Miejsce na uwagi dotyczące realizacji]</p>
        <p>[Miejsce na informacje o reklamacjach]</p>
    </section>
    <footer>
        <p>Podpis osoby sporządzającej:<br>......................................</p>
        <p>Data: <?= date('d-m-Y') ?></p>
    </footer>
    <button onclick="window.print()">Drukuj raport</button>
</div>
</body>
</html>
<?php
} else {
    echo "Nieprawidłowe ID zamówienia.";
}
$conn->close();
?>