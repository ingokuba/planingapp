<?php

class Game extends Entity
{

    /**
     * Entity type/table name.
     *
     * @var string
     */
    public static $GAME = "Game";

    /**
     * Descriptive summary of the task that is planned.
     */
    public static $DESCRIPTION = "description";

    /**
     * Maximum amount of participants in a game.
     * Should only be set on creation.
     */
    public static $MAX_PARTICIPANTS = "maxParticipants";

    /**
     * Result of all played values of the referencing game instances.
     */
    public static $RESULT = "result";

    /**
     * Create timestamp.
     * Read only.
     */
    public static $CREATED_AT = "createdAt";

    protected function initializeEntityType(): string
    {
        return Game::$GAME;
    }

    protected function initializeAttributes(): array
    {
        return array(
            Game::$DESCRIPTION,
            Game::$MAX_PARTICIPANTS,
            Game::$RESULT,
            Game::$CREATED_AT
        );
    }

    /**
     * Constraints:
     * <ul>
     * <li>description
     * <ul>
     * <li>notnullable
     * </ul>
     * <li>maxParticipants
     * <ul>
     * <li>notnullable
     * <li>minimum value 1
     * </ul>
     */
    protected function checkConstraints(): string
    {
        $message = $this->isEmpty(array(
            Game::$DESCRIPTION,
            Game::$MAX_PARTICIPANTS
        ));
        if ($this->getValue(Game::$MAX_PARTICIPANTS) < 1) {
            $message .= "Value must at least be 1.";
        }
        return $message;
    }
}