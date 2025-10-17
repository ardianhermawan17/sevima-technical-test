import crypto from 'crypto';

export function verifySignature(publicKey, data, signatureBase64) {
    const verifier = crypto.createVerify('SHA256');
    verifier.update(data);
    verifier.end();
    return verifier.verify(publicKey, Buffer.from(signatureBase64, 'base64'), 'binary');
}

export function hashData(data) {
    return crypto.createHash('sha256').update(data).digest('hex');
}
