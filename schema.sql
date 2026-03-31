
-- BloodLink – Blood Bank Management System Database Schema


CREATE DATABASE IF NOT EXISTS bloodlink;
USE bloodlink;


--  Users:  Covers all roles: (admin, donor, hospital roles)

CREATE TABLE users (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100) NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    password        VARCHAR(255) NOT NULL,
    role            ENUM('admin', 'donor', 'hospital') NOT NULL,
    phone           VARCHAR(20),
    address         TEXT,
    remember_token  VARCHAR(100),
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


--  Donor profile

CREATE TABLE donor_profiles (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    user_id             INT NOT NULL UNIQUE,
    blood_group         ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    date_of_birth       DATE NOT NULL,
    gender              ENUM('male','female','other') NOT NULL,
    weight_kg           DECIMAL(5,2),
    last_donation_date  DATE,
    next_eligible_date  DATE,
    is_eligible         BOOLEAN DEFAULT TRUE,
    medical_notes       TEXT,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


--  Hospital profile, information (details hospitals, recivers)

CREATE TABLE hospital_profiles (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NOT NULL UNIQUE,
    hospital_name   VARCHAR(150) NOT NULL,
    license_number  VARCHAR(100),
    city            VARCHAR(100),
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);



--   Tracks available units per blood group

CREATE TABLE blood_inventory (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    blood_group     ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL UNIQUE,
    available_units INT DEFAULT 0,
    status          ENUM('available','low','critical') DEFAULT 'available',
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seed inventory rows for all blood groups
INSERT INTO blood_inventory (blood_group, available_units, status) VALUES
('A+',  0, 'critical'),
('A-',  0, 'critical'),
('B+',  0, 'critical'),
('B-',  0, 'critical'),
('AB+', 0, 'critical'),
('AB-', 0, 'critical'),
('O+',  0, 'critical'),
('O-',  0, 'critical');


-- Donations
CREATE TABLE donations (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    donor_id        INT NOT NULL,
    blood_group     ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    units_donated   INT NOT NULL DEFAULT 1,
    donation_date   DATE NOT NULL,
    recorded_by     INT,
    notes           TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (donor_id)    REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL
);

-- blood requests
CREATE TABLE blood_requests (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    hospital_id     INT NOT NULL,
    patient_name    VARCHAR(100) NOT NULL,
    blood_group     ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    units_required  INT NOT NULL,
    urgency         ENUM('normal','urgent','critical') DEFAULT 'normal',
    status          ENUM('pending','approved','rejected','fulfilled') DEFAULT 'pending',
    reviewed_by     INT,
    reviewed_at     TIMESTAMP NULL,
    notes           TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (hospital_id)  REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by)  REFERENCES users(id) ON DELETE SET NULL
);

-- contact
CREATE TABLE contact_submissions (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL,
    role        VARCHAR(50),
    message     TEXT NOT NULL,
    is_read     BOOLEAN DEFAULT FALSE,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- donation events
CREATE TABLE donation_events (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(200) NOT NULL,
    description TEXT,
    location    VARCHAR(255) NOT NULL,
    event_date  DATE NOT NULL,
    start_time  TIME NOT NULL,
    end_time    TIME NOT NULL,
    max_donors  INT,
    status      ENUM('upcoming','ongoing','completed','cancelled') DEFAULT 'upcoming',
    created_by  INT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- register for events
CREATE TABLE event_registrations (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    event_id      INT NOT NULL,
    donor_id      INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    attended      BOOLEAN DEFAULT FALSE,

    UNIQUE KEY unique_registration (event_id, donor_id),
    FOREIGN KEY (event_id) REFERENCES donation_events(id) ON DELETE CASCADE,
    FOREIGN KEY (donor_id) REFERENCES users(id) ON DELETE CASCADE
);

-- volunteers 

CREATE TABLE volunteers (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT,
    full_name    VARCHAR(100) NOT NULL,
    email        VARCHAR(150) NOT NULL,
    phone        VARCHAR(20),
    availability TEXT,
    skills       TEXT,
    status       ENUM('pending','approved','rejected') DEFAULT 'pending',
    reviewed_by  INT,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)     REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- event assignment
CREATE TABLE volunteer_assignments (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    volunteer_id   INT NOT NULL,
    event_id       INT NOT NULL,
    role_at_event  VARCHAR(100),
    assigned_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_assignment (volunteer_id, event_id),
    FOREIGN KEY (volunteer_id) REFERENCES volunteers(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id)     REFERENCES donation_events(id) ON DELETE CASCADE
);



CREATE TABLE personal_access_tokens (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id   INT NOT NULL,
    name           VARCHAR(255) NOT NULL,
    token          VARCHAR(64) NOT NULL UNIQUE,
    abilities      TEXT,
    last_used_at   TIMESTAMP NULL,
    expires_at     TIMESTAMP NULL,
    created_at     TIMESTAMP NULL,
    updated_at     TIMESTAMP NULL,

    INDEX personal_access_tokens_tokenable_type_tokenable_id_index (tokenable_type, tokenable_id)
);