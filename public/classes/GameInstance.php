<?php

/**
 * Represents the participation of a user in a game.
 * (Linking entity)
 *
 */
class GameInstance extends Entity
{

    public static $GAME_INSTANCE = "GameInstance";

    public static $USER_ID = "userID";

    public static $GAME_ID = "gameID";

    public static $CREATED_AT = "createdAt";

    public static $PLAYED_VALUE = "playedValue";

    protected function initializeEntityType(): string
    {
        return GameInstance::$GAME_INSTANCE;
    }

    protected function initializeAttributes(): array
    {
        return array(
            GameInstance::$USER_ID,
            GameInstance::$GAME_ID,
            GameInstance::$PLAYED_VALUE,
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
        $playedValue = $this->getValue(GameInstance::$PLAYED_VALUE);
        if (! ($playedValue == null || in_array($playedValue, Util::$CARDS))) {
            // can be null or a valid card
            $message .= "Value '$playedValue' is not a valid Card. ";
        }
        $existingInstance = $this->database->select(GameInstance::$GAME_INSTANCE, "*", GameInstance::$GAME_ID . "=" . $gameID . " AND " . GameInstance::$USER_ID . "=" . $userID);
        if ($existingInstance != null) {
            $message .= "A link from User $userID to Game $gameID already exists. ";
        }
        $game = new Game($this->database);
        $game = $this->database->select(Game::$GAME, "*", $game->ID . "=" . $gameID);
        if ($game == null) {
            $message .= "Game $gameID not found. ";
        } else {
            $maxParticipants = $game[Game::$MAX_PARTICIPANTS];
            $instanceCount = $this->database->count(GameInstance::$GAME_INSTANCE, "*", GameInstance::$GAME_ID . "=" . $gameID);
            if ($instanceCount >= (int) $maxParticipants) {
                $message .= "Game $gameID already has maximum amount of participants. ";
            }
        }
        return $message;
    }

    /**
     * Determines the numeric value of a card.
     *
     * @param mixed $value
     *            String or numeric value of the card.
     * @return number The value of the card or null if not a number/null.
     */
    public static function getCardValue($value)
    {
        if ($value != null && is_numeric($value)) {
            return $value;
        }
        return 0;
    }
}

