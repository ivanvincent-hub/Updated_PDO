CREATE TABLE tutors (
    tutor_id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100),
    email VARCHAR(100)
);

CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    age INT,
    tutor_id INT,
    FOREIGN KEY (tutor_id)
    REFERENCES tutors(tutor_id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);