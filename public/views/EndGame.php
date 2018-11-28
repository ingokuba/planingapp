<?php

/**
 * View without content.
 */
final class EndGame extends Page
{

    public function output(): string
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $gameID = Util::getPostData(GameInstance::$GAME_ID);
            $result = Util::getPostData(Game::$RESULT);
            if ($result != null && $gameID != null) {
                return $this->handlePost($gameID, $result);
            }
        } else {
            return "Only post requests allowed.";
        }
    }

    /**
     * Sets the result of the referenced game.
     */
    private function handlePost(int $gameID, int $result): string
    {
        if (! $this->database->update(Game::$GAME, "id=$gameID", Game::$RESULT . "=$result")) {
            return "Something went horribly wrong.";
        }
        return "";
    }
}
?>