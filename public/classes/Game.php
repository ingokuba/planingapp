<?php

class Game extends Entity
{

    public static $GAME = "Game";

    public static $ID = "id";

    public static $DESCRIPTION = "description";

    public static $MAX_PARTICIPANTS = "maxParticipants";

    public static $RESULT = "result";

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

    protected function checkConstraints(): string
    {
        return $this->isEmpty(array(
            Game::$DESCRIPTION,
            Game::$MAX_PARTICIPANTS
        ));
    }
}