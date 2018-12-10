<?php
require 'views/Welcome.php';

/**
 * Main view containing the game logic.
 */
class PlayGame extends Welcome
{

    protected function getBody(): string
    {
        if ($this->user == null) {
            header("Location: /");
        }
        $thisUserID = $this->user->getValue("id");
        $thisGameID = Util::getPostData("id");
        $game = new Game($this->database);
        $result = $this->database->select(Game::$GAME, "*", $game->ID . "=$thisGameID");
        $description = $result[Game::$DESCRIPTION];
        $gameResult = $result[Game::$RESULT];
        // played cards:
        $this->view = "<div class='container row mb-2'>
                            <button class='btn btn-lg btn-primary mr-2' data-toggle='tooltip' title='Reload' onclick='location.reload();'>
                                <i class='fas fa-sync'></i>
                            </button>
                            <h3 class='mt-2 mb-3'>$description</h3>
                        </div>
                        <table class='table table-striped table-dark'>
                            <thead>
                                <tr>
                                    <th>Player</th>
                                    <th class='text-right'>Played value</th>
                               <tr>
                            </thead>
                            <tbody>";
        $sum = $i = 0;
        $end = true;
        $result = $this->database->multiSelect(GameInstance::$GAME_INSTANCE, "*", GameInstance::$GAME_ID . "=$thisGameID");
        while ($row = $result->fetch_assoc()) {
            $playedValue = $row[GameInstance::$PLAYED_VALUE];
            if ($playedValue != null) {
                // user played a card
                $sum += GameInstance::getCardValue($playedValue);
                $i ++;
                $playedValueField = "<div class='text-success font-weight-bold'>$playedValue</div>";
                if ($row[GameInstance::$USER_ID] == $thisUserID) {
                    // remember this users playedValue
                    $thisCard = $playedValue;
                }
            } else {
                // user didn't play a card
                $end = false;
                $playedValueField = "<div class='text-danger'><i class='fas fa-times'></i></div>";
            }
            $userID = $row[GameInstance::$USER_ID];
            $user = $this->database->select(User::$USER, "*", "id=$userID");
            $givenName = $user[User::$GIVENNAME];
            $surname = $user[User::$SURNAME];
            $this->view .= "<tr>
                                    <td>$givenName $surname</td>
                                    <td class='text-right'>$playedValueField</td>
                                </tr>";
        }
        if ($end) {
            // Total and average
            $average = round($sum / $i, 1);
            if ($gameResult == null) {
                $endGame = "<script>
                        function endGame() {
                            bootbox.confirm(\"Do you really want to end this game?\", function(result) {
                                $.post(\"EndGame\",
                                {
                                    result: $sum,
                                    gameID: $thisGameID
                                },
                                function(data, status) {
                                    if (data.length > 0) {
                                        bootbox.alert(data);
                                    }
                                    setTimeout(function(){
                                        location.reload();
                                    }, 2000);
                                });
                            });
                        }</script>
                        <button class='btn btn-success text-right' data-toggle='tooltip' title='End game' onclick='endGame()'>
                            <i class='fas fa-flag-checkered'></i>
                        </button>";
            } else {
                $endGame = "Result";
            }
            $this->view .= "<tr class='bg-info font-weight-bold'>
                                <td>$endGame</td>
                                <td class='text-right'>&#8721; $sum (&#8960; $average)</td>
                            </tr>";
        }
        $this->view .= "</tbody></table>";
        if ($gameResult != null) {
            return parent::getBody();
        }
        $this->view .= "<h5 class='font-weight-bold mt-2 mb-3'>Pick your card</h5>
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
                                        bootbox.alert(data);
                                    }
                                    location.reload();
                            });
                        }</script>";
        // play view:
        $indicators = "";
        $inner = "";
        $cards = Util::getCards();
        for ($i = 0; $i < count($cards); $i ++) {
            $card = $cards[$i];
            if (isset($thisCard)) {
                $active = (Util::compare($card, $thisCard) ? "active" : "");
            } else {
                $active = (Util::compare(0, $card) ? "active" : "");
            }
            $indicators .= "<li data-target='#carouselIndicators' data-slide-to='$i' class='$active' data-toggle='tooltip' title='$card'></li>";
            $inner .= "<div class='carousel-item $active'>
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

