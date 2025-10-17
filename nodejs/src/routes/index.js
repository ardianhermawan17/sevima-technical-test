import { Router } from 'express';
import { TranscriptController } from '../domain/transcript/transcript.controller.js';

const router = Router();

router.post('/api/verify-transcript', TranscriptController.verify);
router.get('/health', (_, res) => res.json({ status: 'ok' }));

export default router;
