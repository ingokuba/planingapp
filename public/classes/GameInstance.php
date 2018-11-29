<?php

/**
 * Represents the participation of a user in a game.
 * (Linking entity)
 *
 */
class GameInstance extends Entity
{

    /**
     * Entity type/table name.
     *
     * @var string
     */
    public static $GAME_INSTANCE = "GameInstance";

    /**
     * Id of the referenced user.
     */
    public static $USER_ID = "userID";

    /**
     * Id of the referenced game.
     */
    public static $GAME_ID = "gameID";

    /**
     * Create timestamp.
     * Read only.
     */
    public static $CREATED_AT = "createdAt";

    /**
     * The card played by the player.
     * May be string/number.
     */
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

    /**
     * Constraints:
     * <b>Only one game instance with the same userID and gameID can exist.</b>
     * <ul>
     * <li>userID
     * <ul>
     * <li>notnullable
     * <li>must reference an existing user
     * </ul>
     * <li>gameID
     * <ul>
     * <li>notnullable
     * <li>must reference an existing game
     * </ul>
     * <li>playedValue
     * <ul>
     * <li>either null or a valid card (@see Util::getCards())
     * </ul>
     */
    protected function checkConstraints(): string
    {
        $message = $this->isEmpty(array(
            GameInstance::$USER_ID,
            GameInstance::$GAME_ID
        ));
        $userID = $this->getValue(GameInstance::$USER_ID);
        $user = $this->database->select(User::$USER, "*", "id=" . $userID);
        if ($user == null) {
            $message .= "User $userID not found. ";
        }
        $gameID = $this->getValue(GameInstance::$GAME_ID);
        $playedValue = $this->getValue(GameInstance::$PLAYED_VALUE);
        if (! ($playedValue == null || in_array($playedValue, Util::getCards()))) {
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

