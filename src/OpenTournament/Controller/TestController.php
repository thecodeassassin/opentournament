<?php
/**
 * @author Stephen "TheCodeAssassin" Hoogendijk <admin@tca0.nl>
 */

namespace OpenTournament\Controller;

use OpenTournament\Controller;
use R;
/**
 * Class TestController
 *
 * @package OpenTournament\Controller
 */
class TestController
{
    /**
     * Test action
     *
     * @Route('/test/index(/:param1)(/:param2)')
     * @Route('/alternative-route/index(/:param1)(/:param2)')
     * @Route('/alternative-route2/index/:param1/:param2')
     *
     * @Method('GET')
     * @Name('test.index')
     *
     * @param $param1
     * @param $param2
     *
     *
     */
    public function indexAction($param1 = 1, $param2 = 2)
    {
        echo sprintf('param1: %s, param2: %s', $param1, $param2);

    }

}