-- ==============================================================================
-- SIAP DESA CLOUD - SUPABASE POSTGRESQL MULTI-TENANT SCHEMA
-- Project: NH-Production / SIAP Desa
-- ==============================================================================

-- 1. Central Villages (Tenants)
CREATE TABLE IF NOT EXISTS public.central_villages (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID UNIQUE NOT NULL DEFAULT gen_random_uuid(),
    client_id VARCHAR(80) UNIQUE NOT NULL,
    code VARCHAR(30) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    district VARCHAR(100) NOT NULL,
    regency VARCHAR(100) NOT NULL,
    province VARCHAR(100) NOT NULL,
    head_name VARCHAR(100),
    phone VARCHAR(30),
    email VARCHAR(100),
    status VARCHAR(20) DEFAULT 'ACTIVE',
    address TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- 2. Central Licenses
CREATE TABLE IF NOT EXISTS public.central_licenses (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID UNIQUE NOT NULL DEFAULT gen_random_uuid(),
    central_village_id BIGINT REFERENCES public.central_villages(id) ON DELETE CASCADE,
    license_key VARCHAR(80) UNIQUE NOT NULL,
    locked_client_id VARCHAR(80),
    tier VARCHAR(30) DEFAULT 'ENTERPRISE',
    max_devices INT DEFAULT 10,
    issued_date DATE NOT NULL DEFAULT CURRENT_DATE,
    expiry_date DATE,
    status VARCHAR(20) DEFAULT 'ACTIVE',
    notes TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- 3. Central Sync Mutations Ingestion Stream
CREATE TABLE IF NOT EXISTS public.central_sync_mutations (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID UNIQUE NOT NULL DEFAULT gen_random_uuid(),
    village_code VARCHAR(30) NOT NULL,
    device_code VARCHAR(50) NOT NULL,
    batch_size INT DEFAULT 1,
    payload JSONB,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Row Level Security (RLS) Policies
ALTER TABLE public.central_villages ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.central_licenses ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.central_sync_mutations ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Allow anon select on central_villages" ON public.central_villages FOR SELECT TO anon USING (true);
CREATE POLICY "Allow anon select on central_licenses" ON public.central_licenses FOR SELECT TO anon USING (true);
CREATE POLICY "Allow anon insert on central_sync_mutations" ON public.central_sync_mutations FOR INSERT TO anon WITH CHECK (true);
