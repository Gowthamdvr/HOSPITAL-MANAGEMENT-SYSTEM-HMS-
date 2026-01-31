CREATE DATABASE IF NOT EXISTS edoc;
USE edoc;

-- 1. Admin Table
CREATE TABLE admin (
    aemail VARCHAR(255) PRIMARY KEY,
    apassword VARCHAR(255) NOT NULL
);

-- 2. Specialties Table
CREATE TABLE specialties (
    id INT(2) PRIMARY KEY AUTO_INCREMENT,
    sname VARCHAR(50) NOT NULL
);

-- 3. Doctor Table
CREATE TABLE doctor (
    docid INT(11) PRIMARY KEY AUTO_INCREMENT,
    docemail VARCHAR(255) UNIQUE NOT NULL,
    docname VARCHAR(255) NOT NULL,
    docpassword VARCHAR(255) NOT NULL,
    docnic VARCHAR(15),
    doctel VARCHAR(15),
    specialties INT(2),
    FOREIGN KEY (specialties) REFERENCES specialties(id)
);

-- 4. Patient Table
CREATE TABLE patient (
    pid INT(11) PRIMARY KEY AUTO_INCREMENT,
    pemail VARCHAR(255) UNIQUE NOT NULL,
    pname VARCHAR(255) NOT NULL,
    ppassword VARCHAR(255) NOT NULL,
    paddress VARCHAR(255),
    pnic VARCHAR(15),
    pdob DATE,
    ptel VARCHAR(15)
);

-- 5. Schedule Table
CREATE TABLE schedule (
    scheduleid INT(11) PRIMARY KEY AUTO_INCREMENT,
    docid INT(11),
    title VARCHAR(255),
    scheduledate DATE,
    scheduletime TIME,
    nop INT(4),
    FOREIGN KEY (docid) REFERENCES doctor(docid)
);

-- 6. Appointment Table
CREATE TABLE appointment (
    appoid INT(11) PRIMARY KEY AUTO_INCREMENT,
    pid INT(11),
    apponum INT(3),
    scheduleid INT(11),
    appodate DATE,
    FOREIGN KEY (pid) REFERENCES patient(pid),
    FOREIGN KEY (scheduleid) REFERENCES schedule(scheduleid)
);

-- 7. Webuser Table
CREATE TABLE webuser (
    email VARCHAR(255) PRIMARY KEY,
    usertype CHAR(1) 
    -- a = admin, d = doctor, p = patient
);

-- Insert Default Admin
INSERT INTO admin (aemail, apassword) VALUES ('admin@edoc.com', '$2y$10$VqUCWtxMoaadqwBb1yTRu.x462TgQ5eurMGPOB3dLUkRp3Zakj7PG'); -- password is 'admin123'
INSERT INTO webuser (email, usertype) VALUES ('admin@edoc.com', 'a');

-- Insert Initial Specialties
INSERT INTO specialties (sname) VALUES 
('Accident and emergency medicine'),
('Allergology'),
('Anaesthetics'),
('Biological hematology'),
('Cardiology'),
('Child psychiatry'),
('Clinical biology'),
('Clinical chemistry'),
('Clinical neurophysiology'),
('Clinical radiology'),
('Dental, oral and maxillo-facial surgery'),
('Dermato-venerology'),
('Dermatology'),
('Endocrinology'),
('Gastro-enterology'),
('General hematology'),
('General practice'),
('General surgery'),
('Geriatrics'),
('Gynaeology'),
('Health informatics'),
('Immunology'),
('Infectious diseases'),
('Internal medicine'),
('Laboratory medicine'),
('Midwifery'),
('Nephrology'),
('Neuro-psychiatry'),
('Neurology'),
('Neurosurgery'),
('Nuclear medicine'),
('Obstetrics and gynaecology'),
('Occupational medicine'),
('Ophthalmology'),
('Orthopaedics'),
('Otorhinolaryngology'),
('Paediatric surgery'),
('Paediatrics'),
('Pathology'),
('Pharmacology'),
('Physical medicine and rehabilitation'),
('Plastic surgery'),
('Podiatric Medicine'),
('Podiatric Surgery'),
('Psychiatry'),
('Public health and epidemiology'),
('Radiotherapy'),
('Rehabilitation medicine'),
('Respiratory medicine'),
('Rheumatology'),
('Stomatology'),
('Thoracic surgery'),
('Tropical medicine'),
('Urology'),
('Vascular surgery'),
('Venereology');
-- Insert Sample Doctors
-- Password: doctor123
INSERT INTO doctor (docemail, docname, docpassword, docnic, doctel, specialties) VALUES 
('smith@edoc.com', 'Dr. John Smith', '$2y$10$zl02fZHb7x14aTQLXeCWceAZpySITC7g30nMkRZDZE8Uo8GxEAtEu', '851234567V', '0771234567', 5), -- Cardiology
('jane@edoc.com', 'Dr. Jane Doe', '$2y$10$zl02fZHb7x14aTQLXeCWceAZpySITC7g30nMkRZDZE8Uo8GxEAtEu', '881234567V', '0711234567', 38), -- Paediatrics
('miller@edoc.com', 'Dr. Alan Miller', '$2y$10$zl02fZHb7x14aTQLXeCWceAZpySITC7g30nMkRZDZE8Uo8GxEAtEu', '751234567V', '0721234567', 13); -- Dermatology

-- Insert into webuser for doctors
INSERT INTO webuser (email, usertype) VALUES 
('smith@edoc.com', 'd'),
('jane@edoc.com', 'd'),
('miller@edoc.com', 'd');

-- Insert Sample Patients
-- Password: patient123
INSERT INTO patient (pemail, pname, ppassword, paddress, pnic, pdob, ptel) VALUES 
('patient@edoc.com', 'Test Patient', '$2y$10$mxdq7xiFdvddnbHN6WJX8.ft7Z/Wqg8MHfkc9grYLwvtFQ2kYd/Qq', 'No. 123, Main Street, Colombo', '951234567V', '1995-05-15', '0779876543');

-- Insert into webuser for patients
INSERT INTO webuser (email, usertype) VALUES 
('patient@edoc.com', 'p');

-- Insert Sample Schedules (Sessions)
-- Dates are set for Feb 2026 to ensure they are in the future
INSERT INTO schedule (docid, title, scheduledate, scheduletime, nop) VALUES 
(1, 'Morning Cardiac Clinic', '2026-02-05', '09:00:00', 10),
(1, 'Evening Cardiac Clinic', '2026-02-05', '17:30:00', 10),
(2, 'Paediatric General Checkup', '2026-02-06', '10:00:00', 15),
(3, 'Dermatology consultation', '2026-02-07', '14:00:00', 10),
(2, 'Child Vaccination Session', '2026-02-10', '08:30:00', 20);
