CREATE DATABASE IF NOT EXISTS radovi;

USE radovi;

CREATE TABLE IF NOT EXISTS diplomski_radovi (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    naziv_rada VARCHAR(255) NOT NULL,
    tekst_rada TEXT NOT NULL,
    link_rada VARCHAR(255) NOT NULL,
    oib_tvrtke VARCHAR(11) NOT NULL
);
