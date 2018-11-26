<?php

class Login extends Page
{

    private $error = "";

    protected function getBody(): string
    {
        User::logout();
        $this->handlePost();
        return "<form method='post'
		style='max-width: 330px; margin: auto;'>
		<h3 class='mb-3 font-weight-normal form-text'>Login</h3>
			<div class='form-group row'>
				<input id='email' class='form-control col-sm'
					placeholder='Email' type='email' required='required' name='email'>
			</div>
			<div class='form-group row'>
				<input id='password' class='form-control col-sm'
					placeholder='Password' type='password' required='required' name='password'>
			</div>
			<button id='registerButton' class='btn btn-lg btn-primary btn-block'
				type='submit'>Login</button>
            <div class='text-primary text-center mt-2'><a href='Registration'>New? Create an account.</a></div>
            <div class='row text-danger mt-2'>$this->error</div>
	     </form>";
    }

    /**
     * Login the user with the entered credentials.
     */
    private function handlePost()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $user = new User($this->model);
            // get user credentials from post request:
            foreach (array(
                User::$EMAIL,
                User::$PASSWORD
            ) as $attr) {
                $user->setValue($attr, PlaningController::getPostData($attr));
            }
            // login user:
            $this->error = $user->login();
        }
    }
}