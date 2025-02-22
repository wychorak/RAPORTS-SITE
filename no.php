<?php
// Połączenie z bazą danych MySQL przy użyciu mysqli
$conn = mysqli_connect("localhost", "root", "", "sell"); // Podaj odpowiednie dane logowania

if (!$conn) {
    die("Błąd połączenia: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            // Dodawanie przedmiotu
            $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
            $paid      = mysqli_real_escape_string($conn, $_POST['paid']);
            $sold      = mysqli_real_escape_string($conn, $_POST['sold']);
            $good_bad  = mysqli_real_escape_string($conn, $_POST['good_bad']);
            $size      = mysqli_real_escape_string($conn, $_POST['size']);
            
            $query = "INSERT INTO items (item_name, I_paid, i_sold, good_bad, size) 
                      VALUES ('$item_name', '$paid', '$sold', '$good_bad', '$size')";
            mysqli_query($conn, $query) or die(mysqli_error($conn));
        }
        elseif ($_POST['action'] == 'delete') {
            // Usuwanie przedmiotu – identyfikacja po nazwie
            $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
            $query = "DELETE FROM items WHERE item_name = '$item_name'";
            mysqli_query($conn, $query) or die(mysqli_error($conn));
        }
        elseif ($_POST['action'] == 'edit') {
            // Edycja przedmiotu – identyfikacja po nazwie
            $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
            $paid      = mysqli_real_escape_string($conn, $_POST['paid']);
            $sold      = mysqli_real_escape_string($conn, $_POST['sold']);
            $good_bad  = mysqli_real_escape_string($conn, $_POST['good_bad']);
            $size      = mysqli_real_escape_string($conn, $_POST['size']);
            
            $query = "UPDATE items 
                      SET I_paid = '$paid', i_sold = '$sold', good_bad = '$good_bad', size = '$size'
                      WHERE item_name = '$item_name'";
            mysqli_query($conn, $query) or die(mysqli_error($conn));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Strona Sprzedaży</title>
    <style>
        /* Podstawowy styl CSS */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h2 {
            color: #333;
        }
        .hidden {
            display: none;
        }
        .form-container {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            padding: 10px;
            width: 300px;
        }
        label {
            display: block;
            margin: 5px 0;
        }
        input[type="text"],
        input[type="number"] {
            width: 95%;
            padding: 5px;
        }
        button {
            margin: 5px 0;
            padding: 5px 10px;
        }
        table {
            border-collapse: collapse;
            width: 90%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #eee;
        }
    </style>
</head>
<body>
    <h1>Strona Sprzedaży</h1>
    <!-- Przyciski do wyświetlania formularzy -->
    <button onclick="toggleForm('addForm')">Dodaj przedmiot</button>
    <button onclick="toggleForm('deleteForm')">Usuń przedmiot</button>
    <button onclick="toggleForm('editForm')">Edytuj przedmiot</button>

    <!-- Formularz dodawania przedmiotu -->
    <div id="addForm" class="form-container hidden">
        <h2>Dodaj Przedmiot</h2>
        <form method="post">
            <input type="hidden" name="action" value="add">
            <label>Nazwa przedmiotu:
                <input type="text" name="item_name" required>
            </label>
            <label>I paid:
                <input type="number" name="paid" step="0.01" required>
            </label>
            <label>i sold:
                <input type="number" name="sold" step="0.01" required>
            </label>
            <label>good/bad:
                <input type="text" name="good_bad" required>
            </label>
            <label>size:
                <input type="text" name="size" required>
            </label>
            <button type="submit">Dodaj</button>
        </form>
    </div>

    <!-- Formularz usuwania przedmiotu -->
    <div id="deleteForm" class="form-container hidden">
        <h2>Usuń Przedmiot</h2>
        <form method="post">
            <input type="hidden" name="action" value="delete">
            <label>Nazwa przedmiotu:
                <input type="text" name="item_name" required>
            </label>
            <button type="submit">Usuń</button>
        </form>
    </div>

    <!-- Formularz edycji przedmiotu -->
    <div id="editForm" class="form-container hidden">
        <h2>Edytuj Przedmiot</h2>
        <form method="post">
            <input type="hidden" name="action" value="edit">
            <label>Nazwa przedmiotu (identyfikator):
                <input type="text" name="item_name" required>
            </label>
            <label>Nowe I paid:
                <input type="number" name="paid" step="0.01" required>
            </label>
            <label>Nowe i sold:
                <input type="number" name="sold" step="0.01" required>
            </label>
            <label>Nowe good/bad:
                <input type="text" name="good_bad" required>
            </label>
            <label>Nowe size:
                <input type="text" name="size" required>
            </label>
            <button type="submit">Edytuj</button>
        </form>
    </div>

    <!-- Wyświetlenie tabeli z przedmiotami -->
    <?php
    $result = mysqli_query($conn, "SELECT * FROM items") or die(mysqli_error($conn));
    echo "<h2>Lista Przedmiotów</h2>";
    echo "<table>";
    echo "<tr>
            <th>Nazwa przedmiotu</th>
            <th>I paid</th>
            <th>i sold</th>
            <th>good/bad</th>
            <th>size</th>
            <th>Profit</th>
            <th>Status</th>
          </tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        // Obliczenie profitu: i_sold - I_paid
        $profit = $row['i_sold'] - $row['I_paid'];
        $status = ($profit >= 0) ? "Profit" : "Strata";
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['I_paid']) . "</td>";
        echo "<td>" . htmlspecialchars($row['i_sold']) . "</td>";
        echo "<td>" . htmlspecialchars($row['good_bad']) . "</td>";
        echo "<td>" . htmlspecialchars($row['size']) . "</td>";
        echo "<td>" . $profit . "</td>";
        echo "<td>" . $status . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    ?>

    <!-- Skrypt JavaScript do obsługi wyświetlania formularzy -->
    <script>
        function toggleForm(formId) {
            // Ukryj wszystkie formularze
            var forms = document.getElementsByClassName('form-container');
            for (var i = 0; i < forms.length; i++) {
                forms[i].classList.add('hidden');
            }
            // Pokaż wybrany formularz
            document.getElementById(formId).classList.remove('hidden');
        }
    </script>
</body>
</html>
