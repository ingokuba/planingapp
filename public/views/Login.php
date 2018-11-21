<?php

class Login extends Page
{

    private $error = "";

    public function output(): string
    {
        $this->logout();
        $this->handlePost();
        $this->body = "<form method='post'
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
        return parent::output();
    }

    private function handlePost()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $user = new User($this->model);
            // get user credentials from post request:
            $user->setEmail($this->getPostData('email', 'Email'));
            $user->setPassword($this->getPostData('password', 'Password'));
            // login user:
            $this->error = $user->login();
        }
    }

    /**
     * Get data from the POST request.
     */
    private function getPostData($id, $displayName): string
    {
        $data = $_POST[$id];
        if (! isset($data)) {
            $this->error .= "$displayName is missing. ";
        } else {
            $value = $this->trim_input($data);
            if (empty($value)) {
                $this->error .= "$displayName is missing. ";
            }
            return $value;
        }
        return "";
    }

    private function trim_input($data): string
    {
        $data = trim($data);
        $data = stripslashes($data);
        return htmlspecialchars($data);
    }
}