<?php

class Welcome extends Page
{
    protected $view;
    
    protected function getBody(): string
    {
        if ($this->user == null) {
            header("Location: /");
        }
        $givenname = $this->user->getValue(User::$GIVENNAME);
        $surname = $this->user->getValue(User::$SURNAME);
        $createdAt = date("d.m.Y", strtotime($this->user->getValue(User::$CREATED_AT)));
        return "<nav class='navbar navbar-expand-lg navbar-dark bg-dark'>
		          <div class='collapse navbar-collapse' id='navbarNavAltMarkup'>
			         <div class='navbar-nav'>
				        <a class='nav-item nav-link active' href='/'>Home</a>
				        <a class='nav-item nav-link' href='CreateGame'>New game</a>
			         </div>
		          </div>
	            </nav>
                <div class='row'>
                    <button class='btn btn-lg mb-1' data-toggle='tooltip' title='Logout' onclick='document.cookie=\"User=; expires=new Date(); path=/;\";location.reload();'><i class='fas fa-sign-out-alt'></i></button>
                    <div class='ml-2 pt-3'>Welcome <b>$givenname $surname</b> (Member since $createdAt)</div>
                </div>
               $this->view";
    }
}