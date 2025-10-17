<?php

namespace services;

use core\CryptoManager;

require_once 'CryptoManager.php';

class TranscriptService
{
    private $crypto;
    private $pdo;

    public function __construct(PDO $pdo, string $institutionId)
    {
        $this->pdo = $pdo;
        $this->crypto = new CryptoManager($pdo, $institutionId);
    }

    /**
     * Issue digitally signed transcript
     */
    public function issueTranscript(string $studentNim): string
    {
        $transcriptData = json_encode([
            'student_nim' => $studentNim,
            'semester' => '2023/2024-GANJIL',
            'courses' => [
                ['code' => 'IF-101', 'name' => 'Algoritma', 'grade' => 'A'],
                ['code' => 'IF-202', 'name' => 'Kriptografi', 'grade' => 'A+']
            ],
            'gpa' => 3.95
        ]);

        $signatureData = $this->crypto->signTranscript($transcriptData);

        $uuid = bin2hex(random_bytes(16));
        $stmt = $this->pdo->prepare(
            "INSERT INTO verified_transcripts 
            (transcript_id, student_nim, signature, transcript_hash) 
            VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $uuid,
            $studentNim,
            $signatureData['signature'],
            $signatureData['transcript_hash']
        ]);

        return json_encode([
            'transcript_id' => $uuid,
            'transcript' => base64_encode($transcriptData),
            'signature' => base64_encode($signatureData['signature'])
        ]);
    }
}