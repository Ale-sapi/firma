<?php
header('Content-Type: application/json');

// Percorso del file dove memorizzeremo i dati
$dataFile = 'strings.txt';

// Funzione per leggere i dati dal file
function readData() {
    global $dataFile;
    if (file_exists($dataFile)) {
        $content = file_get_contents($dataFile);
        return $content ? explode("\n", trim($content)) : [];
    }
    return [];
}

// Funzione per salvare i dati nel file
function saveData($data) {
    global $dataFile;
    file_put_contents($dataFile, implode("\n", $data));
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Restituisce tutte le stringhe
        echo json_encode(readData());
        break;

    case 'POST':
        // Aggiunge una nuova stringa
        $data = readData();
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['string'])) {
            $data[] = $input['string'];
            saveData($data);
            echo json_encode(['success' => true]);
        }
        break;

    case 'DELETE':
        // Elimina una stringa
        $data = readData();
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['index'])) {
            array_splice($data, $input['index'], 1);
            saveData($data);
            echo json_encode(['success' => true]);
        }
        break;
}
?> 