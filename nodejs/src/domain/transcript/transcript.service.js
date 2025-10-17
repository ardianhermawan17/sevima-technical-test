import { TranscriptRepository } from './transcript.repository.js';
import { verifySignature, hashData } from '../../utils/crypto.js';
import { env } from '../../config/env.js';

export const TranscriptService = {
    async verify(transcript_id, transcript_data, signature) {
        const institutionId = env.institutionId;

        const publicKey = await TranscriptRepository.getPublicKey(institutionId);
        if (!publicKey) throw new Error('Institution key not found');

        const transcript = await TranscriptRepository.getTranscript(transcript_id);
        if (!transcript) throw new Error('Transcript not found');

        const isSignatureValid = verifySignature(publicKey, transcript_data, signature);
        const isHashValid = hashData(transcript_data) === transcript.transcript_hash.toString('hex');

        return {
            valid: isSignatureValid && isHashValid,
            transcript_id,
            institution: institutionId,
        };
    },
};
