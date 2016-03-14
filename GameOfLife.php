<?php

namespace arichard;

class GameOfLife {

    protected $previousState;
    protected $currentState;
    protected $gridHeight;
    protected $gridWidth;

    /**
     * set up the game
     *
     * The seed should be in the following format:
     *
     * $seed = [
     * [true, true, true, true, true, false, false],
     * [true, true, true, true, true, false, false],
     * [true, true, true, true, true, false, false],
     * [true, true, true, true, true, false, false],
     * [true, true, true, true, true, false, false],
     * [true, true, true, true, true, false, false],
     * ]
     *
     * @param array $seed=null
     */
    public function __construct($seed = null)
    {
        if (empty($seed)) {
            $seed = $this->makeGameOfLifeSeed(25, 25);
        }

        $this->setCurrentState( $seed );
    }

    /**
     * Advanced the game one round
     *
     * @return void
     */
    public function oneGameRound()
    {
        $inState      = $this->getCurrentState();
        $outState     = $inState;
        $maxNeighbors = 8;

        for ($y = 0; $y < $this->gridHeight; ++$y) {
            for ($x = 0; $x < $this->gridWidth; ++$x) {
                $livingNeighborCount = 0;

                for ($n = 0; $n < $maxNeighbors; ++$n) {
                    // get the neighbor
                    $neighborCoords = $this->getNeighborCoords($n, $x, $y);

                    // correct dimentions for edges, we wrap around the grid to check
                    // the neighbor over there, if needed.
                    $correctedCoords = $this->correctNeighborCoords($neighborCoords['x'], $neighborCoords['y'], $this->gridWidth, $this->gridHeight);

                    // now that we know where our neighbor resides, let's see if they are
                    // alive or not
                    $livingNeighborCount += ( !empty($inState[$correctedCoords['x']][$correctedCoords['y']]) ? 1 : 0);
                }

                // determine the new state of this cell based on the number of living
                // neighbors
                if ($inState[$x][$y]) {
                    $outState[$x][$y] = ( $livingNeighborCount < 2 || $livingNeighborCount > 3 ? false : true);
                } else {
                    $outState[$x][$y] = (3 == $livingNeighborCount ? true : false);
                }
            }
        }

        $this->setPreviousState( $inState );
        $this->setCurrentState( $outState );
    }

    /**
     * get the current state
     * @return array currentState
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }

    /**
     * set the current state
     * @param array $state the state to set
     * @return GameOfLife
     */
    public function setCurrentState($state)
    {
        $this->currentState = $state;
        $this->gridWidth    = count($this->currentState);
        $this->gridHeight   = count($this->currentState[0]);
        return $this;
    }

    /**
     * get the current state
     * @return array the previous state
     */
    public function getPreviousState()
    {
        return $this->previousState;
    }

    /**
     * set the previous state
     * @param array $state the state to set
     * @return GameOfLife
     */
    public function setPreviousState($state)
    {
        $this->previousState = $state;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGridHeight() {
        return $this->gridHeight;
    }

    /**
     * @param mixed $gridHeight
     */
    public function setGridHeight( $gridHeight ) {
        $this->gridHeight = $gridHeight;
    }

    /**
     * @return mixed
     */
    public function getGridWidth() {
        return $this->gridWidth;
    }

    /**
     * @param mixed $gridWidth
     */
    public function setGridWidth( $gridWidth ) {
        $this->gridWidth = $gridWidth;
    }

    /**
     * Displays the current state of the game board
     */
    public function displayGameBoard()
    {
        if ( empty( $this->currentState) ) {
            throw new \Exception( 'Game has not been initialized' );
        }

        for ($y = 0; $y < $this->gridHeight; ++$y) {
            if (0 != $y) {
                echo PHP_EOL;
            }

            for ($x = 0; $x < $this->gridWidth; ++$x) {
                echo ( $this->currentState[$x][$y] ? html_entity_decode('&#x2588;', ENT_NOQUOTES, 'UTF-8') : html_entity_decode('&#x2591;', ENT_NOQUOTES, 'UTF-8') );
            }
        }

        echo PHP_EOL . PHP_EOL;
    }

    /**
     * Get coordinates of the neighbor (n) from current coordinates (homeX, homeY)
     *
     * work our way around the grid checking this cell's neighbors
     * starting with the bottom left neightbor and working around
     * counter clockwise
     *
     * @param int $neighbor - neighbor we are locating
     * @param int $homeX current x coord
     * @param int $homeY current y coord
     * @return array coordinates of neighbor
     */
    protected function getNeighborCoords($neighbor, $homeX, $homeY) {
        $newX = $homeX;
        $newY = $homeY;

        switch ($neighbor) {
            /*
             * +-+-+-+
             * | | | |
             * +-+-+-+
             * | |H| |
             * +-+-+-+
             * |N| | |
             * +-+-+-+
             */
            case 0:
                $newY -= 1;
                $newX -= 1;
                break;
                /*
                 * +-+-+-+
                 * | | | |
                 * +-+-+-+
                 * | |H| |
                 * +-+-+-+
                 * | |N| |
                 * +-+-+-+
                 */
            case 1:
                $newY -= 1;
                // newX no change
                break;
                /*
                 * +-+-+-+
                 * | | | |
                 * +-+-+-+
                 * | |H| |
                 * +-+-+-+
                 * | | |N|
                 * +-+-+-+
                 */
            case 2:
                $newY -= 1;
                $newX += 1;
                break;
                /*
                 * +-+-+-+
                 * | | | |
                 * +-+-+-+
                 * | |H|N|
                 * +-+-+-+
                 * | | | |
                 * +-+-+-+
                 */
            case 3:
                // newY no change
                $newX += 1;
                break;
                /*
                 * +-+-+-+
                 * | | |N|
                 * +-+-+-+
                 * | |H| |
                 * +-+-+-+
                 * | | | |
                 * +-+-+-+
                 */
            case 4:
                $newY += 1;
                $newX += 1;
                break;
                /*
                 * +-+-+-+
                 * | |N| |
                 * +-+-+-+
                 * | |H| |
                 * +-+-+-+
                 * | | | |
                 * +-+-+-+
                 */
            case 5:
                $newY += 1;
                // newX no change
                break;
                /*
                 * +-+-+-+
                 * |N| | |
                 * +-+-+-+
                 * | |H| |
                 * +-+-+-+
                 * | | | |
                 * +-+-+-+
                 */
            case 6:
                $newY += 1;
                $newX -= 1;
                break;
                /*
                 * +-+-+-+
                 * | | | |
                 * +-+-+-+
                 * |N|H| |
                 * +-+-+-+
                 * | | | |
                 * +-+-+-+
                 */
            case 7:
                // newY no change
                $newX -= 1;
                break;
        }

        $out = [
            'x' => $newX,
            'y' => $newY,
        ];

        return $out;
    }

    /**
     * Correct the coordinated of the neighbor if needed
     *
     * @param int $nX neighbor's x coord
     * @param int $nY neighbor's y coord
     * @param int $gridWidth width of grid/plane
     * @param int $gridHeight height of grid/plane
     * @return array corrected (possibly wrapping) coords
     */
    protected function correctNeighborCoords($nX, $nY, $gridWidth, $gridHeight) {
        $nX = ($nX < 0) ? ($gridWidth + $nX) : $nX;
        $nX = ($nX >= $gridWidth) ? ($nX - $gridWidth) : $nX;

        $nY = ($nY < 0) ? ($gridHeight + $nY) : $nY;
        $nY = ($nY >= $gridHeight) ? ($nY - $gridHeight) : $nY;

        $out = [
            'x' => $nX,
            'y' => $nY,
        ];

        return $out;
    }

    /**
     * generates a seed for the game
     *
     * @param int $gridWidth width of grid
     * @param int $gridHeight height of grid
     * @param int $maxLiving=0 max number of living cells
     * @return array configured seed
     */
    public static function makeGameOfLifeSeed($gridWidth, $gridHeight, $maxLiving = 0) {
        $seed = [];

        $livingCount = 0;
        for ($y = 0; $y < $gridHeight; ++$y) {
            for ($x = 0; $x < $gridWidth; ++$x) {
                $aliveOrDead = mt_rand(0,1); // ( mt_rand(0,90) % 10 == 0 ? 1 : 0 );
                if (1 === $aliveOrDead && $maxLiving > 0) {
                    // since we care about how many living we have we need to see how
                    // many living cells we have, if we have too many, this one must
                    // die
                    $aliveOrDead = ($livingCount >= $maxLiving ? 0 : 1);
                }
                $livingCount += $aliveOrDead;
                $seed[$x][$y] = (1 === $aliveOrDead ? true : false);
            }
        }

        return $seed;
    }
}
