-- Migration pour ajouter les champs ville_de_provenance et activity_id à la table beneficiaries
-- RAMP-BENIN

USE ramp_benin;

-- Ajouter le champ ville_de_provenance
ALTER TABLE beneficiaries 
ADD COLUMN IF NOT EXISTS ville_de_provenance VARCHAR(100) NULL AFTER address;

-- Ajouter le champ activity_id
ALTER TABLE beneficiaries 
ADD COLUMN IF NOT EXISTS activity_id INT NULL AFTER project_id;

-- Ajouter la clé étrangère pour activity_id
ALTER TABLE beneficiaries 
ADD CONSTRAINT fk_beneficiary_activity 
FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE SET NULL;

-- Ajouter un index pour activity_id
CREATE INDEX IF NOT EXISTS idx_activity ON beneficiaries(activity_id);

