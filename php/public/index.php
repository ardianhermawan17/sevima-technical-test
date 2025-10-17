<?php
// public/index.php
declare(strict_types=1);

use services\TranscriptService;

require_once __DIR__.'/../vendor/autoload.php';

try {
    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s',
        getenv('DB_HOST') ?: 'postgres',
        getenv('DB_PORT') ?: '5432',
        getenv('DB_DATABASE') ?: 'sevima_bagian_i_soal_1'
    );

    $pdo = new PDO(
        $dsn,
        getenv('DB_USERNAME') ?: 'sevima_user',
        getenv('DB_PASSWORD') ?: 'sevima12345'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_URI'] === '/health') {
        $pdo->query('SELECT 1');
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok', 'database' => 'connected']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/api/transcripts') {
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['student_nim'])) {
            http_response_code(400);
            echo json_encode(['error' => 'student_nim is required']);
            exit;
        }

        $service = new TranscriptService($pdo, getenv('INSTITUTION_ID') ?: 'UNIVERSITAS-INDONESIA');
        $transcript = $service->issueTranscript($input['student_nim']);

        header('Content-Type: application/json');
        echo $transcript;
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/api/keys') {
        $core = new core\CryptoManager($pdo, getenv('INSTITUTION_ID') ?: 'UNIVERSITAS-INDONESIA');
        $core->generateKeyPair();

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => '2048-bit keys generated']);
        exit;
    }

    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed', 'details' => $e->getMessage()]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Application error', 'details' => $e->getMessage()]);
}