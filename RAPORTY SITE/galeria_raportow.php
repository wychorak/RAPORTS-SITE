<?php
$conn = new mysqli("serwer2448789.home.pl", "38270794_wychor", "2T7STsFp*jjtz", "38270794_wychor", "3380");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$sql = "
    SELECT 
        z.ID_Zamowienia AS zamowienie_id,
        z.Data_Zamowienia AS data_zlozenia,
        k.ID_Klienta AS klient_id,
        CONCAT(k.Imie, ' ', k.Nazwisko) AS klient_nazwa,
        SUM(p.Cena * zp.Ilosc) AS cena_zamowienia
    FROM zamowienia z
    JOIN klienci k ON z.ID_Klienta = k.ID_Klienta
    JOIN zamowienia_produkty zp ON z.ID_Zamowienia = zp.ID_Zamowienia
    JOIN produkty p ON zp.ID_Produktu = p.ID_Produktu
    GROUP BY z.ID_Zamowienia, z.Data_Zamowienia, k.ID_Klienta, k.Imie, k.Nazwisko
    ORDER BY z.Data_Zamowienia DESC
";

$zamowienia = $conn->query($sql);

if ($zamowienia === false) {
    die("Błąd zapytania: " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Galeria Raportów</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style_galeria.css">
</head>
<body>
    <div class="header">
        <div class="buttons-container">
            <a href="main.html" class="btn-main">
                <i class="fas fa-home"></i> Wróć do menu
            </a>
        </div>
    </div>
    <div class="container">
        <div class="galeria" id="galeria">
            <?php while ($zam = $zamowienia->fetch_assoc()): ?>
                <div class="kafelek" data-id="<?= $zam['zamowienie_id'] ?>">
                    <div class="kafelek-header">
                        <i class="fas fa-file-invoice"></i>
                        <h3>Zamówienie #<?= $zam['zamowienie_id'] ?></h3>
                    </div>
                    <div class="kafelek-content">
                        <p><i class="fas fa-calendar-day"></i> <?= date('d-m-Y', strtotime($zam['data_zlozenia'])) ?></p>
                        <p><i class="fas fa-user-tag"></i> <?= htmlspecialchars($zam['klient_nazwa']) ?></p>
                        <p><i class="fas fa-coins"></i> <?= number_format($zam['cena_zamowienia'], 2) ?> PLN</p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <div class="raport" id="raportContainer"></div>
    </div>
    <script>
        document.querySelectorAll('.kafelek').forEach(item => {
            item.addEventListener('click', function () {
                const id = this.dataset.id;
                fetch(`pobierz_raport.php?id=${id}`)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('raportContainer').innerHTML = `
                            <button class="powrot-btn">
                                <i class="fas fa-arrow-left"></i> Powrót do galerii
                            </button>
                            ${html}
                        `;
                        document.querySelector('.container').classList.add('pokaz-raport');
                    })
                    .catch(error => console.error('Błąd:', error));
            });
        });

        document.getElementById('raportContainer').addEventListener('click', function (e) {
            if (e.target.closest('.powrot-btn')) {
                document.querySelector('.container').classList.remove('pokaz-raport');
                this.innerHTML = '';
            }
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>