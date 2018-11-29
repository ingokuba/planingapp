<?php

/**
 * Contentless POST handler to create a game instance for the invited player.
 */
class InvitePlayer extends Page
{

    /**
     * Handles the post request and builds resulting error message.
     *
     * @return string Error message or empty string.
     */
    public function output(): string
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = Util::getPostData(User::$EMAIL);
            $gameID = Util::getPostData(GameInstance::$GAME_ID);
            if ($email != null && $gameID != null) {
                return $this->handlePost($email, $gameID);
            }
        } else {
            header("Location: /");
        }
    }

    /**
     * Creates a new game instance for the invited user.
     */
    private function handlePost(string $email, int $gameID): string
    {
        if ($email == $this->user->getValue(User::$EMAIL)) {
            return "You cannot invite yourself";
        }
        $user = $this->database->select(User::$USER, "*", User::$EMAIL . "='$email'");
        if ($user == null) {
            return "User not found";
        }
        $userID = $user["id"];
        $instance = $this->database->select(GameInstance::$GAME_INSTANCE, "*", GameInstance::$GAME_ID . "=$gameID AND " . GameInstance::$USER_ID . "=$userID");
        if ($instance != null) {
            return "User was already invited";
        }
        $instance = new GameInstance($this->database);
        $instance->setValue(GameInstance::$GAME_ID, $gameID);
        $instance->setValue(GameInstance::$USER_ID, $userID);
        // store the new instance
        try {
            return $instance->store();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
?>