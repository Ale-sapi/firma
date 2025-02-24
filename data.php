<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// File dove verranno salvate le stringhe
$file = 'stringhe.json';

// Se il file non esiste, crealo
if (!file_exists($file)) {
    file_put_contents($file, '[]');
    chmod($file, 0666);
}

// Funzione per leggere le stringhe dal file
function leggiStringhe() {
    global $file;
    $contenuto = file_get_contents($file);
    if ($contenuto === false) {
        return [];
    }
    $dati = json_decode($contenuto);
    return is_array($dati) ? $dati : [];
}

// Funzione per salvare le stringhe nel file
function salvaStringhe($stringhe) {
    global $file;
    return file_put_contents($file, json_encode($stringhe)) !== false;
}

// Gestione delle richieste
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Restituisce tutte le stringhe
        echo json_encode(leggiStringhe());
        break;

    case 'POST':
        // Aggiunge una nuova stringa
        $input = file_get_contents('php://input');
        $data = json_decode($input);
        
        if (!$data || !isset($data->stringa)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Dati non validi']);
            break;
        }

        if (strlen($data->stringa) > 5) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Stringa troppo lunga']);
            break;
        }

        $stringhe = leggiStringhe();
        $stringhe[] = $data->stringa;
        
        if (salvaStringhe($stringhe)) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Errore nel salvataggio']);
        }
        break;

    case 'DELETE':
        // Elimina una stringa
        $input = file_get_contents('php://input');
        $data = json_decode($input);
        
        if (!$data || !isset($data->stringa)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Dati non validi']);
            break;
        }

        $stringhe = leggiStringhe();
        $index = array_search($data->stringa, $stringhe);
        
        if ($index !== false) {
            array_splice($stringhe, $index, 1);
            if (salvaStringhe($stringhe)) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Errore nella cancellazione']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Stringa non trovata']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Metodo non permesso']);
}
?> 