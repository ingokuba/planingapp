USE planingdb;

INSERT INTO User (givenName, surname, email, password)
VALUES ('Simon', 'Frank', 'mail@test.de', 'test');

INSERT INTO Game(description)
VALUES ('Demo game');

INSERT INTO GameInstance(userID, gameID)
VALUES (1, 1);