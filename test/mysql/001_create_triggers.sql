use planningdb;

DELIMITER $$

CREATE TRIGGER deleteGameInstance_user AFTER DELETE on User
FOR EACH ROW
BEGIN
	DELETE FROM GameInstance
	WHERE GameInstance.userID = old.id;
END $$

CREATE TRIGGER deleteGameInstance_game AFTER DELETE on Game
FOR EACH ROW
BEGIN
	DELETE FROM GameInstance
	WHERE GameInstance.gameID = old.id;
END $$

DELIMITER ;