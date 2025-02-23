<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "raporty");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Pobierz listę produktów i klientów
$produkty = $conn->query("
    SELECT ID_Produktu AS produkt_id, Nazwa AS nazwa, Cena_Retail AS cena_zakupu, Cena AS cena_sprzedazy 
    FROM Produkty
");

$klienci = $conn->query("SELECT ID_Klienta AS klient_id, CONCAT(Imie, ' ', Nazwisko) AS klient_nazwa FROM Klienci");

// Przekonwertuj produkty na tablicę asocjacyjną
$produkty_array = [];
while ($produkt = $produkty->fetch_assoc()) {
    $produkty_array[] = $produkt;
}

// Obsługa formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobierz dane z formularza
    $data_zlozenia = $_POST['data_zlozenia'];
    $klient_id = intval($_POST['klient_id']);

    // Wstaw zamówienie do tabeli Zamowienia
    $stmt_zamowienie = $conn->prepare("INSERT INTO Zamowienia (ID_Klienta, Data_Zamowienia) VALUES (?, ?)");
    if (!$stmt_zamowienie) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt_zamowienie->bind_param("is", $klient_id, $data_zlozenia);
    if (!$stmt_zamowienie->execute()) {
        die("Execute failed: " . $stmt_zamowienie->error);
    }
    $id_zamowienia = $stmt_zamowienie->insert_id;
    $stmt_zamowienie->close();

    // Dodaj produkty do tabeli Zamowienia_Produkty
    if (isset($_POST['produkt_id']) && is_array($_POST['produkt_id'])) {
        foreach ($_POST['produkt_id'] as $index => $produkt_id) {
            $ilosc_produktow = intval($_POST['ilosc_produktow'][$index]);
            $stmt_produkty = $conn->prepare("INSERT INTO Zamowienia_Produkty (ID_Zamowienia, ID_Produktu, Ilosc) VALUES (?, ?, ?)");
            if (!$stmt_produkty) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt_produkty->bind_param("iii", $id_zamowienia, $produkt_id, $ilosc_produktow);
            if (!$stmt_produkty->execute()) {
                die("Execute failed: " . $stmt_produkty->error);
            }
            $stmt_produkty->close();
        }
    }

    echo "<script>alert('Zamówienie zostało dodane!'); window.location.href = 'galeria_raportow.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Utwórz Raport</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style_utworz_raport.css">
</head>
<body>
    <div class="header">
        <div class="buttons-container">
            <a href="main.html" class="btn-main">
                <i class="fas fa-home"></i> Wróć do menu
            </a>
            <a href="galeria_raportow.php" class="btn-main">
                <i class="fas fa-list"></i> Wróć do galerii raportów
            </a>
        </div>
    </div>
    <div class="container">
        <h1>Utwórz Raport</h1>
        <form method="POST" action="utworz_raport.php" id="orderForm">
            <div class="form-group">
                <label for="data_zlozenia"><i class="fas fa-calendar-day"></i> Data złożenia:</label>
                <input type="datetime-local" id="data_zlozenia" name="data_zlozenia" value="<?= date('Y-m-d\TH:i') ?>" required>
            </div>
            <div class="form-group">
                <label for="klient_id"><i class="fas fa-user-tie"></i> Klient:</label>
                <select id="klient_id" name="klient_id" required>
                    <?php while ($klient = $klienci->fetch_assoc()): ?>
                        <option value="<?= $klient['klient_id'] ?>">
                            <?= htmlspecialchars($klient['klient_nazwa']) ?> (ID: <?= $klient['klient_id'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div id="productRows">
                <!-- Dynamiczne wiersze produktów -->
                <div class="product-row">
                    <div class="form-group">
                        <label><i class="fas fa-box-open"></i> Produkt:</label>
                        <select class="product-select" name="produkt_id[]" required>
                            <?php foreach ($produkty_array as $produkt): ?>
                                <option value="<?= $produkt['produkt_id'] ?>" data-cena-sprzedazy="<?= $produkt['cena_sprzedazy'] ?>" data-cena-zakupu="<?= $produkt['cena_zakupu'] ?>">
                                    <?= htmlspecialchars($produkt['nazwa']) ?> (ID: <?= $produkt['produkt_id'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="product-info">Koszt sprzedaży: 0 PLN | Profit ze sztuki: 0 PLN</p>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-hashtag"></i> Ilość:</label>
                        <input type="number" class="quantity-input" name="ilosc_produktow[]" min="1" value="1" required>
                    </div>
                    <button type="button" class="remove-row btn-remove"><i class="fas fa-trash-alt"></i></button>
                </div>
            </div>
            <button type="button" class="add-product btn-add">
                <i class="fas fa-plus"></i> Dodaj produkt
            </button>
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Utwórz Raport
            </button>
        </form>
    </div>

    <script>
        // Przekazanie listy produktów do JavaScript
        const produktyJSON = <?php echo json_encode($produkty_array); ?>;

        // Funkcja dodawania nowego wiersza produktu
        document.querySelector('.add-product').addEventListener('click', function () {
            const productRowTemplate = `
                <div class="product-row">
                    <div class="form-group">
                        <label><i class="fas fa-box-open"></i> Produkt:</label>
                        <select class="product-select" name="produkt_id[]" required>
                            ${produktyJSON.map(produkt => `
                                <option value="${produkt.produkt_id}" data-cena-sprzedazy="${produkt.cena_sprzedazy}" data-cena-zakupu="${produkt.cena_zakupu}">
                                    ${produkt.nazwa} (ID: ${produkt.produkt_id})
                                </option>
                            `).join('')}
                        </select>
                        <p class="product-info">Koszt sprzedaży: 0 PLN | Profit ze sztuki: 0 PLN</p>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-hashtag"></i> Ilość:</label>
                        <input type="number" class="quantity-input" name="ilosc_produktow[]" min="1" value="1" required>
                    </div>
                    <button type="button" class="remove-row btn-remove"><i class="fas fa-trash-alt"></i></button>
                </div>
            `;
            document.getElementById('productRows').insertAdjacentHTML('beforeend', productRowTemplate);
            updateProductInfo();
        });

        // Usuwanie wiersza produktu
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-row')) {
                e.target.closest('.product-row').remove();
            }
        });

        // Aktualizacja informacji o produkcie (koszt sprzedaży i profit)
        function updateProductInfo() {
            document.querySelectorAll('.product-select').forEach(function (select) {
                select.addEventListener('change', function () {
                    const selectedOption = this.options[this.selectedIndex];
                    const cenaSprzedazy = parseFloat(selectedOption.getAttribute('data-cena-sprzedazy'));
                    const cenaZakupu = parseFloat(selectedOption.getAttribute('data-cena-zakupu'));
                    const profit = cenaSprzedazy - cenaZakupu;
                    const productInfo = this.nextElementSibling;
                    productInfo.textContent = `Koszt sprzedaży: ${cenaSprzedazy.toFixed(2)} PLN | Profit ze sztuki: ${profit.toFixed(2)} PLN`;
                });
            });
        }

        // Inicjalizacja aktualizacji informacji o produkcie
        updateProductInfo();
    </script>
</body>
</html>
<?php $conn->close(); ?>