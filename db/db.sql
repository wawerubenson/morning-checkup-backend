CREATE DATABASE IF NOT EXISTS morning_checkup;

USE morning_checkup;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    lastname_name VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE pbx_checkups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pbx_name VARCHAR(50) NOT NULL,
    date DATE NOT NULL,
    incoming_calls_status VARCHAR(20) NOT NULL,
    outgoing_calls_status VARCHAR(20) NOT NULL,
    total_storage VARCHAR(20) NOT NULL,
    storage_used VARCHAR(20) NOT NULL,
    storage_remaining VARCHAR(20) NOT NULL,
    web_interface_status VARCHAR(20) NOT NULL
);

-- Insert data for PBX1 to PBX7 for 2024-07-01
INSERT INTO pbx_checkups (pbx_name, date, incoming_calls_status, outgoing_calls_status, storage_usage, storage_remaining, web_interface_status) VALUES
('PBX1', '2024-07-01', 'Working', 'Working', '30 GB', '70 GB', 'Accessible'),
('PBX2', '2024-07-01', 'Working', 'Not Working', '40 GB', '60 GB', 'Not Accessible'),
('PBX3', '2024-07-01', 'Working', 'Not Working', '50 GB', '50 GB', 'Accessible'),
('PBX4', '2024-07-01', 'Working', 'Working', '20 GB', '80 GB', 'Accessible'),
('PBX5', '2024-07-01', 'Not Working', 'Not Working', '60 GB', '40 GB', 'Not Accessible'),
('PBX6', '2024-07-01', 'Working', 'Not Working', '70 GB', '30 GB', 'Accessible'),
('PBX7', '2024-07-01', 'Working', 'Working', '10 GB', '90 GB', 'Accessible');

-- Insert data for PBX1 to PBX7 for 2024-07-02
INSERT INTO pbx_checkups (pbx_name, date, incoming_calls_status, outgoing_calls_status, storage_usage, storage_remaining, web_interface_status) VALUES
('PBX1', '2024-07-02', 'Not Working', 'Working', '35 GB', '65 GB', 'Not Accessible'),
('PBX2', '2024-07-02', 'Working', 'Working', '45 GB', '55 GB', 'Accessible'),
('PBX3', '2024-07-02', 'Working', 'Not Working', '55 GB', '45 GB', 'Accessible'),
('PBX4', '2024-07-02', 'Working', 'Working', '25 GB', '75 GB', 'Accessible'),
('PBX5', '2024-07-02', 'Not Working', 'Working', '65 GB', '35 GB', 'Not Accessible'),
('PBX6', '2024-07-02', 'Working', 'Working', '75 GB', '25 GB', 'Accessible'),
('PBX7', '2024-07-02', 'Not Working', 'Working', '15 GB', '85 GB', 'Not Accessible');

-- Insert data for PBX1 to PBX7 for 2024-07-03
INSERT INTO pbx_checkups (pbx_name, date, incoming_calls_status, outgoing_calls_status, storage_usage, storage_remaining, web_interface_status) VALUES
('PBX1', '2024-07-03', 'Working', 'Not Working', '40 GB', '60 GB', 'Accessible'),
('PBX2', '2024-07-03', 'Working', 'Working', '50 GB', '50 GB', 'Not Accessible'),
('PBX3', '2024-07-03', 'Not Working', 'Working', '60 GB', '40 GB', 'Accessible'),
('PBX4', '2024-07-03', 'Working', 'Working', '30 GB', '70 GB', 'Accessible'),
('PBX5', '2024-07-03', 'Working', 'Not Working', '70 GB', '30 GB', 'Not Accessible'),
('PBX6', '2024-07-03', 'Not Working', 'Working', '80 GB', '20 GB', 'Accessible'),
('PBX7', '2024-07-03', 'Working', 'Working', '20 GB', '80 GB', 'Accessible');


CREATE TABLE synology_checkups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    synology_name VARCHAR(50) NOT NULL,
    date DATE NOT NULL,
    hyperbackup_status VARCHAR(20) NOT NULL,
    yesterdays_recordings_status VARCHAR(20) NOT NULL,
    storage_usage VARCHAR(20) NOT NULL,
    storage_remaining VARCHAR(20) NOT NULL,
    web_interface_status VARCHAR(20) NOT NULL
);

-- Insert data for green_breeze, PKF2, and Xivodrive for 2024-07-01
INSERT INTO synology_checkups (synology_name, date, hyperbackup_status, yesterdays_recordings_status, storage_usage, storage_remaining, web_interface_status) VALUES
('green_breeze', '2024-07-01', 'Running', 'Available', '80 GB', '120 GB', 'Accessible'),
('PKF2', '2024-07-01', 'Paused', 'Not Available', '90 GB', '110 GB', 'Not Accessible'),
('Xivodrive', '2024-07-01', 'Running', 'Available', '70 GB', '130 GB', 'Accessible');

-- Insert data for green_breeze, PKF2, and Xivodrive for 2024-07-02
INSERT INTO synology_checkups (synology_name, date, hyperbackup_status, yesterdays_recordings_status, storage_usage, storage_remaining, web_interface_status) VALUES
('green_breeze', '2024-07-02', 'Paused', 'Available', '85 GB', '115 GB', 'Accessible'),
('PKF2', '2024-07-02', 'Running', 'Available', '95 GB', '105 GB', 'Not Accessible'),
('Xivodrive', '2024-07-02', 'Paused', 'Not Available', '75 GB', '125 GB', 'Accessible');

-- Insert data for green_breeze, PKF2, and Xivodrive for 2024-07-03
INSERT INTO synology_checkups (synology_name, date, hyperbackup_status, yesterdays_recordings_status, storage_usage, storage_remaining, web_interface_status) VALUES
('green_breeze', '2024-07-03', 'Running', 'Not Available', '90 GB', '110 GB', 'Accessible'),
('PKF2', '2024-07-03', 'Paused', 'Available', '100 GB', '100 GB', 'Not Accessible'),
('Xivodrive', '2024-07-03', 'Running', 'Available', '80 GB', '120 GB', 'Accessible');
