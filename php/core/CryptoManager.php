<?php

namespace core;
class CryptoManager
{
    private $pdo;
    private $institutionId;

    public function __construct(\PDO $pdo, string $institutionId)
    {
        $this->pdo = $pdo;
        $this->institutionId = $institutionId;
    }

    /**
     * Generate and store 2048-bit RSA key pair
     */
    public function generateKeyPair(): void
    {
        $config = [
            "digest_alg" => "sha256",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];

        $resource = openssl_pkey_new($config);
        openssl_pkey_export($resource, $privateKey, null, $config);

        $details = openssl_pkey_get_details($resource);
        $publicKey = $details['key'];

        // Store as big-endian binary (native database storage)
        $privateBin = openssl_pkey_get_private($privateKey);
        $publicBin = openssl_get_publickey($publicKey);

        $stmt = $this->pdo->prepare(
            "INSERT INTO academic_keys (institution_id, private_key, public_key) 
            VALUES (?, ?, ?)"
        );
        $stmt->execute([
            $this->institutionId,
            $this->pkeyToBinary($privateBin),
            $this->pkeyToBinary($publicBin)
        ]);
    }

    /**
     * Sign academic transcript with 2048-bit RSA
     */
    public function signTranscript(string $transcriptData): array
    {
        $transcriptHash = hash('sha256', $transcriptData, true);

        $stmt = $this->pdo->prepare(
            "SELECT private_key FROM academic_keys WHERE institution_id = ?"
        );
        $stmt->execute([$this->institutionId]);
        $privateKeyBin = $stmt->fetchColumn();

        $privateKey = $this->binaryToPkey($privateKeyBin);
        openssl_sign($transcriptHash, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        return [
            'signature' => $signature,
            'transcript_hash' => $transcriptHash
        ];
    }

    /**
     * Verify transcript signature (used internally)
     */
    public function verifyTranscript(string $transcriptData, string $signature): bool
    {
        $transcriptHash = hash('sha256', $transcriptData, true);

        $stmt = $this->pdo->prepare(
            "SELECT public_key FROM academic_keys WHERE institution_id = ?"
        );
        $stmt->execute([$this->institutionId]);
        $publicKeyBin = $stmt->fetchColumn();

        $publicKey = $this->binaryToPkey($publicKeyBin, true);
        return openssl_verify($transcriptHash, $signature, $publicKey) === 1;
    }

    // Helper: Convert PHP key resource to binary
    private function pkeyToBinary($keyResource, bool $isPublic = false): string
    {
        if ($isPublic) {
            openssl_pkey_export($keyResource, $pem);
            return $pem; // Public key is stored as PEM (binary-safe)
        }

        openssl_pkey_export($keyResource, $pem);
        return $pem; // Private key as PEM (binary-safe)
    }

    // Helper: Convert binary to PHP key resource
    private function binaryToPkey(string $binary, bool $isPublic = false): mixed
    {
        return $isPublic
            ? openssl_get_publickey($binary)
            : openssl_pkey_get_private($binary);
    }
}