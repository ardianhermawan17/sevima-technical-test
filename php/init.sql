CREATE TABLE IF NOT EXISTS academic_keys (
    id SERIAL PRIMARY KEY,
    institution_id VARCHAR(50) NOT NULL UNIQUE,
    private_key BYTEA NOT NULL,
    public_key BYTEA NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS verified_transcripts (
    transcript_id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    student_nim VARCHAR(20) NOT NULL,
    signature BYTEA NOT NULL,
    transcript_hash BYTEA NOT NULL,
    academic_year VARCHAR(9) NOT NULL,
    semester VARCHAR(6) NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO academic_keys (institution_id, public_key)
VALUES ('UNIVERSITAS-INDONESIA', 'wokwowowkowk');
