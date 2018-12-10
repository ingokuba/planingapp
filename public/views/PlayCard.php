<?php

/**
 * Contentless POST handler to update the played card of a game instance.
 */
final class PlayCard extends Page
{

    /**
     * Handles the post request and builds resulting error message.
     *
     * @return string Error message or empty string.
     */
    public function output(): string
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $card = Util::getPostData(GameInstance::$PLAYED_VALUE);
            $gameID = Util::getPostData(GameInstance::$GAME_ID);
            $userID = Util::getPostData(GameInstance::$USER_ID);
            if ($card != null && $gameID != null && $userID != null) {
                return $this->handlePost($card, $gameID, $userID);
            }
        } else {
            header("Location: /");
        }
    }

    /**
     * Sets the played value of the referenced game instance.
     */
    private function handlePost(string $card, int $gameID, int $userID): string
    {
        $result = $this->database->select(Game::$GAME, "*", "id=$gameID");
        if ($result == null) {
            return "Game doesn't exist.";
        } else if ($result[Game::$RESULT] != null) {
            return "Game is already closed.";
        }
        if (! $this->database->update(GameInstance::$GAME_INSTANCE, GameInstance::$GAME_ID . "=$gameID AND " . GameInstance::$USER_ID . "=$userID", GameInstance::$PLAYED_VALUE . "='$card'")) {
            return "Something went horribly wrong.";
        }
        return "";
    }
}

