<?php
require 'views/Welcome.php';

/**
 * View with input form to create a new game entity and instance for the logged in user.
 */
class CreateGame extends Welcome
{

    /**
     * Error message that should be displayed when something failed.
     *
     * @var string
     */
    private $error = "";

    protected function getBody(): string
    {
        $this->handlePost();
        $this->view = "<form method='post'
		style='max-width: 330px; margin: auto;'>
			<div class='form-group mt-3'>
                <label for='description'>Description</label>
				<textarea id='description' class='form-control col-sm' rows='3'
					required name='description'></textarea>
			</div>
			<div class='form-group mb-3>
                <label for='maxParticipants'>Max. participants</label>
				<input id='maxParticipants' class='form-control col-sm' type='number' min='0' value='4'
			         placeholder='Max. participants' required name='maxParticipants'>
			</div>
			<button class='btn btn-lg btn-primary btn-block'
				type='submit'>Create</button>
            <div class='row text-danger mt-2'>$this->error</div>
	     </form>";
        return parent::getBody();
    }

    /**
     * Retrieves game attributes from the request parameters and creates the game and instance with them.
     */
    private function handlePost()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $game = new Game($this->database);
            // get game data from post request:
            foreach (array(
                Game::$DESCRIPTION,
                Game::$MAX_PARTICIPANTS
            ) as $attribute) {
                $game->setValue($attribute, Util::getPostData($attribute));
            }
            // store game:
            try {
                $this->error = $game->store();
            } catch (Exception $e) {
                $this->error = $e->getMessage();
                return;
            }
            // create game instance aswell:
            $instance = new GameInstance($this->database);
            $instance->setValue(GameInstance::$GAME_ID, $game->getValue($game->ID));
            $instance->setValue(GameInstance::$USER_ID, $this->user->getValue($this->user->ID));
            // store instance:
            try {
                $this->error .= $instance->store();
            } catch (Exception $e) {
                $this->error .= $e->getMessage();
            }
            if (empty($this->error)) {
                header("Location: /");
            }
        }
    }
}