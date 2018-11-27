<?php
require 'views/Welcome.php';

class PlayGame extends Welcome
{

    protected function getBody(): string
    {
        $thisGameID = Util::getPostData("id");
        $game = new Game($this->database);
        $result = $this->database->select(Game::$GAME, "*", $game->ID . "=$thisGameID");
        $description = $result[Game::$DESCRIPTION];
        // played cards:
        $this->view = "<h3 class='mt-2 mb-3'>$description</h3>
                        <table class='table table-hover table-dark'>
                            <thead>
                                <tr>
                                    <th>Played cards</th>
                               <tr>
                            </thead>
                            <tbody>";

        $result = $this->database->multiSelect(GameInstance::$GAME_INSTANCE, "*", GameInstance::$GAME_ID . "=$thisGameID");
        while ($row = $result->fetch_assoc()) {
            $playedValue = $row[GameInstance::$PLAYED_VALUE] == null ? "<div class='text-danger'><i class='fas fa-times'></i></div>" : "<div class='text-success font-weight-bold'>" . $row[GameInstance::$PLAYED_VALUE] . "</div>";
            $userID = $row[GameInstance::$USER_ID];
            $user = $this->database->select(User::$USER, "*", "id=$userID");
            $givenName = $user[User::$GIVENNAME];
            $surname = $user[User::$SURNAME];
            $this->view .= "<tr>
                                    <td>$givenName $surname</td>
                                    <td class='text-right'>$playedValue</td>
                                </tr>";
        }
        $thisUserID = $this->user->getValue("id");
        $this->view .= "</tbody></table>
                        <h5 class='mt-2 mb-3'>Pick your card</h5>
                        <script>
                        function playCard(card) {
                            $.post(\"PlayCard\",
                                {
                                playedValue: card,
                                gameID: $thisGameID,
                                userID: $thisUserID
                                },
                                function(data, status) {
                                    if (data.length > 0) {
                                        alert(data);
                                    }
                                    location.reload();
                            });
                        }</script>";
        // play view:
        $indicators = "";
        $inner = "";
        for ($i = 0; $i < count(Util::$CARDS); $i ++) {
            $card = Util::$CARDS[$i];
            $indicators .= "<li data-target='#carouselIndicators' data-slide-to='$i' " . ($i == 0 ? "class='active'" : "") . "
                            data-toggle='tooltip' title='$card'></li>";
            $inner .= "<div class='carousel-item" . ($i == 0 ? " active" : "") . "'>
                            <div class='d-block w-100 text-center' onclick='playCard(\"$card\")'>                                
                                <img class='w-25 mt-3 mb-5' src='resources/card.png' width='50%' alt='Card $card'>
                                <div class='h1 text-white' style='position: absolute; top: 45%; left: 0; width: 100%;'>$card</div>
                            </div>
                        </div>";
        }
        $this->view .= "<div id='carouselIndicators' class='carousel slide bg-dark' data-ride='carousel' data-interval='false'>
                            <ol class='carousel-indicators'>$indicators</ol>
                        <div class='carousel-inner'>$inner</div>
                        <a class='carousel-control-prev' href='#carouselIndicators' role='button' data-slide='prev'>
                            <span class='carousel-control-prev-icon' aria-hidden='true'></span>
                            <span class='sr-only'>Previous</span>
                        </a>
                        <a class='carousel-control-next' href='#carouselIndicators' role='button' data-slide='next'>
                            <span class='carousel-control-next-icon' aria-hidden='true'></span>
                            <span class='sr-only'>Next</span>
                        </a>
                    </div>";
        return parent::getBody();
    }
}

