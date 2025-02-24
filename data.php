<?php
header('Content-Type: application/json');

// Percorso del file JSON che memorizza i dati
$file = 'strings.json';

// Funzione per leggere i dati dal file
function readData() {
    global $file;
    if (file_exists($file)) {
        $content = file_get_contents($file);
        return json_decode($content, true) ?: [];
    }
    return [];
}

// Funzione per salvare i dati nel file
function saveData($data) {
    global $file;
    file_put_contents($file, json_encode($data));
}

// Gestione delle richieste in base al metodo HTTP
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Restituisce tutte le stringhe
        echo json_encode(readData());
        break;

    case 'POST':
        // Aggiunge una nuova stringa
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['string']) && strlen($data['string']) <= 5) {
            $strings = readData();
            $strings[] = $data['string'];
            saveData($strings);
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Stringa non valida']);
        }
        break;

    case 'DELETE':
        // Elimina una stringa
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['index'])) {
            $strings = readData();
            if (isset($strings[$data['index']])) {
                array_splice($strings, $data['index'], 1);
                saveData($strings);
                echo json_encode(['success' => true]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Indice non trovato']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Indice non specificato']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Metodo non permesso']);
        break;
}
?> 