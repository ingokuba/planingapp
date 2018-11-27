<?php

/**
 * View without content.
 */
class InvitePlayer extends Page
{

    public function output(): string
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = PlaningController::getPostData(User::$EMAIL);
            $gameID = PlaningController::getPostData(GameInstance::$GAME_ID);
            if (! empty($email) && ! empty($gameID)) {
                return $this->handlePost($email, $gameID);
            }
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
        $user = $this->model->select(User::$USER, "*", User::$EMAIL . "='$email'");
        if ($user == null) {
            return "User not found";
        }
        $userID = $user["id"];
        $instance = $this->model->select(GameInstance::$GAME_INSTANCE, "*", GameInstance::$GAME_ID . "=$gameID AND " . GameInstance::$USER_ID . "=$userID");
        if ($instance != null) {
            return "User was already invited";
        }
        $instance = new GameInstance($this->model);
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