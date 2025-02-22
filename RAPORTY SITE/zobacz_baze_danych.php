<?php
// Połączenie z bazą danych
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raporty";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pobieranie danych z tabel
$klienci = $conn->query("SELECT * FROM klienci");
$produkty = $conn->query("SELECT * FROM produkty");
$zamowienia = $conn->query("SELECT * FROM zamowienia");
$zamowienia_produkty = $conn->query("SELECT * FROM zamowienia_produkty");

$conn->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zobacz Bazę Danych</title>
    <link rel="stylesheet" href="../css/style_zobacz_baze_danych.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</head>
<body>
    <div class="container">
        <h1>Baza Danych "Raporty"</h1>

        <!-- Przyciski nawigacyjne -->
        <div class="nav-buttons">
            <a href="main.html">Powrót do Menu</a>
            <a href="galeria_raportow.php">Galeria Raportów</a>
        </div>

        <!-- Tabela Klienci -->
        <div class="table-container" id="klienci">
            <h2><i class="fas fa-users"></i> Klienci</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID_Klienta</th>
                        <th>Imie</th>
                        <th>Nazwisko</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Adres</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $klienci->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['ID_Klienta']; ?></td>
                            <td><?php echo $row['Imie']; ?></td>
                            <td><?php echo $row['Nazwisko']; ?></td>
                            <td><?php echo $row['Email']; ?></td>
                            <td><?php echo $row['Telefon']; ?></td>
                            <td><?php echo $row['Adres']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="action-buttons">
                <button onclick="location.href='dodaj_do_bazy.php?table=klienci'">Dodaj Klienta</button>
            </div>
        </div>

        <!-- Tabela Produkty -->
        <div class="table-container" id="produkty">
            <h2><i class="fas fa-box"></i> Produkty</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID_Prodkutu</th>
                        <th>Nazwa</th>
                        <th>Cena</th>
                        <th>Opis</th>
                        <th>Dostepnosc</th>
                        <th>Cena_Retail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $produkty->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['ID_Prodkutu']; ?></td>
                            <td><?php echo $row['Nazwa']; ?></td>
                            <td><?php echo $row['Cena']; ?></td>
                            <td><?php echo $row['Opis']; ?></td>
                            <td><?php echo $row['Dostepnosc']; ?></td>
                            <td><?php echo $row['Cena_Retail']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="action-buttons">
                <button onclick="location.href='dodaj_do_bazy.php?table=produkty'">Dodaj Produkt</button>
            </div>
        </div>

        <!-- Tabela Zamowienia -->
        <div class="table-container" id="zamowienia">
            <h2><i class="fas fa-receipt"></i> Zamówienia</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID_Zamowienia</th>
                        <th>ID_Klienta</th>
                        <th>Data_Zamowienia</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $zamowienia->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['ID_Zamowienia']; ?></td>
                            <td><?php echo $row['ID_Klienta']; ?></td>
                            <td><?php echo $row['Data_Zamowienia']; ?></td>
                            <td><?php echo $row['Status']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Tabela Zamowienia_Produkty -->
        <div class="table-container" id="zamowienia-produkty">
            <h2><i class="fas fa-cart-plus"></i> Zamówienia Produkty</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID_Zamowienia</th>
                        <th>ID_Prodkutu</th>
                        <th>Ilosc</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $zamowienia_produkty->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['ID_Zamowienia']; ?></td>
                            <td><?php echo $row['ID_Prodkutu']; ?></td>
                            <td><?php echo $row['Ilosc']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Animacja pojawiania się tabel
        document.querySelectorAll('.table-container').forEach((table, index) => {
            setTimeout(() => {
                table.style.opacity = '1';
                table.style.transform = 'translateY(0)';
            }, 200 * (index + 1));
        });
    </script>
</body>
</html>