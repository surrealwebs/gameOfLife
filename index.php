<?php
/**
 * Game of life tester
 *
 * @author arichard <arichard@nerdery.com>
 */
require_once( 'GameOfLife.php');
require_once( 'TestSeeds.php' );

// defined the size of the game, used when generating a random seed
$gridWidth    = 100;
$gridHeight   = 25;

// generations to run
$generations  = 1250;

// determines how fast/slow the animate is (in microseconds)
$duration = 20000; // 40000;

// generate a random game state "Seed"
// $seedState = \arichard\GameOfLife::makeGameOfLifeSeed($gridWidth, $gridHeight);
$seedState = unserialize(base64_decode(file_get_contents('savedSeed-1457990744.5331.seed')));
// determine if we should save the seed or not, typically we only want to save
// new random seeds, anything else we have at least some record of.
$saveSeed = false;
// use one of the static seeds (I recommend the COLUMNS_25x25)
// $seedState = \arichard\TestSeeds::$COLUMNS_25x25;

$game = new \arichard\GameOfLife($seedState);

// display the initial (seeded) game state
$game->displayGameBoard();

// total output chars
$totalChars = $game->getGridWidth() * $game->getGridHeight();

for ( $g = 0; $g < $generations; ++$g ) {
    usleep( $duration );
    $game->oneGameRound();

    // check to see if we have stalled
    if ($seedState !== $game->getCurrentState() && $game->getCurrentState() == $game->getPreviousState()) {
        break;
    }

    // back the cursor up so we can overwrite to look animated
    echo "\033[" . ($game->getGridHeight() + 1) . 'A';
    echo "\033[" . $game->getGridWidth() . 'D';
    $game->displayGameBoard();
}

if ($saveSeed) {
    file_put_contents( 'savedSeed-' . microtime(true) . '.seed' , base64_encode(serialize($seedState)) );
}
