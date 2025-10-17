import { TranscriptService } from './transcript.service.js';

export const TranscriptController = {
    async verify(req, res) {
        const { transcript_id, transcript_data, signature } = req.body;

        if (!transcript_id || !transcript_data || !signature) {
            return res.status(400).json({ error: 'Missing required fields' });
        }

        try {
            const result = await TranscriptService.verify(transcript_id, transcript_data, signature);
            res.json(result);
        } catch (err) {
            res.status(500).json({ error: err.message });
        }
    },
};
