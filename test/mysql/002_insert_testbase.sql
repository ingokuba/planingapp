USE planningdb;

INSERT INTO User (givenName, surname, email, password)
VALUES ('Tim', 'Buktu', 'mail@test.com', 'test');

INSERT INTO Game(description)
VALUES ('Demo game');

INSERT INTO GameInstance(userID, gameID)
VALUES (1, 1);