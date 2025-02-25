<?php
// Carica i dati dal file JSON
$data = json_decode(file_get_contents('data.json'), true);

// Se non ci sono dati, inizializza un array vuoto
if (!$data) {
    $data = [];
}

// Gestione della cancellazione
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    // Filtra i dati per rimuovere la riga con l'id specificato
    $data = array_filter($data, function($row) use ($deleteId) {
        return $row['id'] !== $deleteId;
    });
    // Rimuove eventuali indici mancanti dopo il filtro
    $data = array_values($data);
    // Salva i dati aggiornati
    file_put_contents('data.json', json_encode($data, JSON_PRETTY_PRINT));
    // Ricarica la pagina dopo la cancellazione
    header('Location: index.php');
    exit;
}

// Aggiungi una nuova riga (se il form è stato inviato)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newId = $_POST['new_id'];
    $data[] = ['id' => $newId];
    // Salva i dati aggiornati
    file_put_contents('data.json', json_encode($data, JSON_PRETTY_PRINT));
    // Ricarica la pagina dopo l'aggiunta
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabella Modificabile</title>
</head>
<body>

<h1>Tabella Modificabile</h1>

<!-- Tabella -->
<table border="1">
    <thead>
        <tr>
            <th>ID (5 cifre)</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><a href="?delete=<?= htmlspecialchars($row['id']) ?>">Cancella</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Form per aggiungere una nuova riga -->
<h2>Aggiungi una nuova riga</h2>
<form method="POST">
    <label for="new_id">ID (5 cifre):</label>
    <input type="text" id="new_id" name="new_id" maxlength="5" required>
    <button type="submit">Aggiungi</button>
</form>

</body>
</html>
