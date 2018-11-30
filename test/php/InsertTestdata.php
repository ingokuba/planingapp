<?php
chdir("public");

$classes = getcwd() . "/classes";
foreach (scandir($classes) as $filename) {
    $path = "$classes/" . $filename;
    if (is_file($path)) {
        require $path;
    }
}

$database = new Database();
$user = new User($database);
$user->setValue(User::$GIVENNAME, "Tim");
$user->setValue(User::$SURNAME, "Tester");
$user->setValue(User::$EMAIL, "tester@test.com");
$user->setValue(User::$PASSWORD, "test");
$message = $user->store();

$userID = $user->getValue("id");

$game = new Game($database);
$game->setValue(Game::$DESCRIPTION, "Demo game");
$game->setValue(Game::$MAX_PARTICIPANTS, 4);
$message .= $game->store();

$gameID = $game->getValue("id");

$gameInstance = new GameInstance($database);
$gameInstance->setValue(GameInstance::$USER_ID, $userID);
$gameInstance->setValue(GameInstance::$GAME_ID, $gameID);
$message .= $gameInstance->store();

echo $message;
?>