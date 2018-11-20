CREATE DATABASE planingdb;

USE planingdb;

CREATE TABLE User (
userID int(11) NOT NULL AUTO_INCREMENT,
givenName varchar(512) NOT NULL,
surname varchar(512) NOT NULL,
email varchar(254) NOT NULL UNIQUE,
password varchar(254) NOT NULL,
createdAt timestamp DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (userID)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;