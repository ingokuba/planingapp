<?php

/**
 * View without content.
 */
final class PlayCard extends Page
{

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
            return "Only post requests allowed.";
        }
    }

    /**
     * Creates a new game instance for the invited user.
     */
    private function handlePost(string $card, int $gameID, int $userID): string
    {
        if (! $this->database->update(GameInstance::$GAME_INSTANCE, GameInstance::$GAME_ID . "=$gameID AND " . GameInstance::$USER_ID . "=$userID", GameInstance::$PLAYED_VALUE . "='$card'")) {
            return "Something went horribly wrong.";
        }
        return "";
    }
}
