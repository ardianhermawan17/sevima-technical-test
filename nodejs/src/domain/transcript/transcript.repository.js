import { pool } from '../../config/database.js';

export const TranscriptRepository = {
    async getPublicKey(institutionId) {
        const [rows] = await pool.query(
            `SELECT public_key FROM academic_keys WHERE institution_id = ?`,
            [institutionId]
        );
        return rows.length ? rows[0].public_key : null;
    },

    async getTranscript(transcriptId) {
        const [rows] = await pool.query(
            `SELECT transcript_id, transcript_hash FROM verified_transcripts WHERE transcript_id = ?`,
            [transcriptId]
        );
        return rows.length ? rows[0] : null;
    },
};
