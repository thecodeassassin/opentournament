<?php
/**
 * @author Stephen "TheCodeAssassin" Hoogendijk <admin@tca0.nl>
 */

namespace OpenTournament;


abstract class Middleware extends \Slim\Middleware
{

    /**
     * @var App
     */
    public $app;

    /**
     * @return App
     */
    public function getApp()
    {
        return $this->app;
    }
}