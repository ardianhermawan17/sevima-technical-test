import dotenv from 'dotenv';
dotenv.config();

export const env = {
    app: {
        name: process.env.APP_NAME || 'transcript-verification',
        env: process.env.APP_ENV || 'local',
        port: process.env.PORT || 3000,
    },
    db: {
        host: process.env.DB_HOST || 'localhost',
        port: process.env.DB_PORT || 3306,
        user: process.env.DB_USERNAME || 'root',
        password: process.env.DB_PASSWORD || '',
        database: process.env.DB_DATABASE || 'siakad_db',
    },
    institutionId: process.env.INSTITUTION_ID || 'UNIVERSITAS-INDONESIA',
};
