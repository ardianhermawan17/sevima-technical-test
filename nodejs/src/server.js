import app from './app.js';
import { env } from './config/env.js';

app.listen(env.app.port, () => {
    console.log(`✅ ${env.app.name} running on port ${env.app.port}`);
});
