CREATE DATABASE crud;

USE crud;
CREATE TABLE testtable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data1 VARCHAR(50),
    data2 VARCHAR(50),
    data3 VARCHAR(50)
);

INSERT INTO testtable (data1, data2, data3) VALUES 
('Vrijednost1', 'Vrijednost2', 'Vrijednost3'),
('Test1', 'Test2', 'Test3'),
('Primjer1', 'Primjer2', 'Primjer3');
