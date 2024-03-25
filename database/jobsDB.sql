-- Use this to create your jobs table
CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    contact_email VARCHAR(255) NOT NULL
    -- Add any additional fields if required by your application logic
);

-- Use this to create your users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
    -- Add any additional fields if required by your application logic
);
INSERT INTO jobs (title, description, location, start_date, contact_email) VALUES
('Software Engineer', 'Responsible for developing enterprise-level solutions...', 'New York', '2024-01-01', 'hiring@company.com'),
('Web Developer', 'Join our dynamic team to build innovative web applications...', 'San Francisco', '2024-02-15', 'apply@webco.com');
INSERT INTO users (email, password_hash) VALUES
('admin@example.com', '<hashed_password_here>');
