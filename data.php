<?php
header('Content-Type: application/json');

// File dove verranno salvate le stringhe
$file = 'stringhe.json';

// Funzione per leggere le stringhe dal file
function leggiStringhe() {
    global $file;
    if (file_exists($file)) {
        $contenuto = file_get_contents($file);
        return json_decode($contenuto) ?: [];
    }
    return [];
}

// Funzione per salvare le stringhe nel file
function salvaStringhe($stringhe) {
    global $file;
    file_put_contents($file, json_encode($stringhe));
}

// Gestione delle richieste
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Restituisce tutte le stringhe
        echo json_encode(leggiStringhe());
        break;

    case 'POST':
        // Aggiunge una nuova stringa
        $data = json_decode(file_get_contents('php://input'));
        if (isset($data->stringa) && strlen($data->stringa) <= 5) {
            $stringhe = leggiStringhe();
            $stringhe[] = $data->stringa;
            salvaStringhe($stringhe);
            echo json_encode(['success' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Stringa non valida']);
        }
        break;

    case 'DELETE':
        // Elimina una stringa
        $data = json_decode(file_get_contents('php://input'));
        if (isset($data->stringa)) {
            $stringhe = leggiStringhe();
            $index = array_search($data->stringa, $stringhe);
            if ($index !== false) {
                array_splice($stringhe, $index, 1);
                salvaStringhe($stringhe);
                echo json_encode(['success' => true]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Stringa non trovata']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Stringa non specificata']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Metodo non permesso']);
}
?> 