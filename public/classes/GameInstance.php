<?php

/**
 * Represents the participation of a user in a game.
 * (Linking entity)
 *
 */
class GameInstance extends Entity
{

    public static $GAME_INSTANCE = "GameInstance";

    public static $ID = "id";

    public static $USER_ID = "userID";

    public static $GAME_ID = "gameID";

    public static $CREATED_AT = "createdAt";

    protected function initializeEntityType(): string
    {
        return GameInstance::$GAME_INSTANCE;
    }

    protected function initializeAttributes(): array
    {
        return array(
            GameInstance::$USER_ID,
            GameInstance::$GAME_ID,
            GameInstance::$CREATED_AT
        );
    }

    protected function checkConstraints(): string
    {
        $message = $this->isEmpty(array(
            GameInstance::$USER_ID,
            GameInstance::$GAME_ID
        ));
        $userID = $this->getValue(GameInstance::$USER_ID);
        $gameID = $this->getValue(GameInstance::$GAME_ID);
        $existingInstance = $this->model->select(GameInstance::$GAME_INSTANCE, "*", GameInstance::$GAME_ID . "=" . $gameID . " AND " . GameInstance::$USER_ID . "=" . $userID);
        if ($existingInstance != null) {
            $message .= "A link from User $userID to Game $gameID already exists. ";
        }
        $game = $this->model->select(Game::$GAME, "*", Game::$ID . "=" . $gameID);
        if ($game == null) {
            $message .= "Game $gameID not found. ";
        } else {
            $maxParticipants = $game[Game::$MAX_PARTICIPANTS];
            $instances = $this->model->select(GameInstance::$GAME_INSTANCE, "*", GameInstance::$GAME_ID . "=".$gameID);
            if ($instances != null && count($instances) >= $maxParticipants) {
                $message .= "Game $gameID already has maximum amount of participants. ";
            }
        }
        return $message;
    }
}

