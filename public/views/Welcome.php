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
        $this->initView();
        return "<nav class='navbar navbar-expand navbar-dark bg-dark'>
		          <div class='collapse navbar-collapse' id='navbarNavAltMarkup'>
			         <div class='navbar-nav'>
				        <a class='nav-item nav-link active' href='/'>Overview</a>
				        <a class='nav-item nav-link' href='CreateGame'>New game</a>
			         </div>
		          </div>
	            </nav>
                <div class='container row mt-3 mb-3'>
                    <button class='btn btn-lg btn-danger' data-toggle='tooltip' title='Logout' onclick='document.cookie=\"User=; expires=new Date(); path=/;\";window.location=\"/\";'><i class='fas fa-sign-out-alt'></i></button>
                    <div class='ml-2 pt-3'>Welcome <b>$givenname $surname</b> (Member since $createdAt)</div>
                </div>
               $this->view";
    }

    private function initView(): void
    {
        if ($this->view == null) {
            $this->view = "<script>
                            function promptPlayer(gameID) {
                                var user = prompt(\"Please enter email\");
                                if (user != null) {
                                    $.post(\"InvitePlayer\",
                                    {
                                        email: user,
                                        gameID: gameID
                                    },
                                    function(data, status) {
                                        if (data.length > 0) {
                                            alert(data);
                                        }
                                    });
                                }
                            }
                            function playGame(gameID) {
                                form = $(\"<form method='post' action='PlayGame'><input type='hidden' value='\" + gameID + \"' name='id'/></form>\");
                                $(document.body).append(form);
                                form.submit();
                            }
                            </script>
                            <h3 class='mt-2 mb-3'>Your games:</h3>
                            <table class='table table-hover table-dark'>
                            <thead>
                                <tr>
                                    <th style='width: 60%'>Description</th>
                                    <th style='width: 20%'>Created</th>
                                    <th style='width: 10%'>Result</th>
                                    <th style='width: 10%'>Action</th>
                               <tr>
                            </thead>
                            <tbody>";
            $instances = $this->database->query("SELECT * FROM " . GameInstance::$GAME_INSTANCE . " WHERE " . GameInstance::$USER_ID . "=" . $this->user->getValue($this->user->ID));
            while ($instance = $instances->fetch_assoc()) {
                $gameID = $instance[GameInstance::$GAME_ID];
                $game = $this->database->select(Game::$GAME, "*", "id=" . $gameID);
                $createdAt = date("d.m.Y h:ia", $game[Game::$CREATED_AT]);
                $result = $game[Game::$RESULT] == null ? 
                // Play game:
                "<button class='btn btn-primary' onclick='playGame($gameID)' data-toggle='tooltip' title='Play'>
                                                            <i class='fas fa-play'></i>
                                                        </button>" : 
                // View game:
                $game[Game::$RESULT] . "<i class='fas fa-eye ml-1' onclick='playGame($gameID)' data-toggle='tooltip' title='View'></i>";
                $action = $game[Game::$RESULT] == null ? "<button class='btn btn-success' 
                    onclick='promptPlayer($gameID)' data-toggle='tooltip' title='Invite'><i class='fas fa-plus-square'></i></button>" : "";
                $this->view .= "<tr>
                                    <td>" . $game[Game::$DESCRIPTION] . "</td>
                                    <td>$createdAt</td>
                                    <td>$result</td>
                                    <td>$action</td>
                                </tr>";
            }
            $this->view .= "</tbody></table>";
        }
    }
}