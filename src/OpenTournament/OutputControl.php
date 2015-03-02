<?php
/**
 * @author Stephen "TheCodeAssassin" Hoogendijk <admin@tca0.nl>
 */

namespace OpenTournament;


class OutputControl extends Middleware
{
    public function call()
    {

        $app = $this->app;

        // set the default output to json
        $app->response->header('Content-Type', 'application/json');

        $app->error(function ( \Exception $e ) use ($app) {
            $app->jsonResponse->exception($e);
        });

        $app->notFound(function() use ($app) {
            $app->jsonResponse->notFound();
        });

        // call the next middleware
        $this->next->call();
    }

}