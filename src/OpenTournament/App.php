<?php
/**
 * @author Stephen "TheCodeAssassin" Hoogendijk <admin@tca0.nl>
 */

namespace OpenTournament;

use OpenTournament\App\Exception;
use R;
use Slim\Slim;

/**
 * Custom APP class
 *
 * @package OpenTournament
 */
class App extends Slim
{

    /**
     * {@inheritdoc}
     */
    public function __construct($userSettings = array())
    {
        parent::__construct($userSettings);

        if (!is_file(CONFIG_DIR.'/config.php')) {
            throw new \Exception('No config file found!');
        }

        $this->response->header('Content-Type', 'application/json');

        // setup the database
        R::setup($this->config['database']['dsn'], $this->config['database']['username'], $this->config['database']['password']);

        // Custom OpenTournament singletons
        $this->registerMiddleware();
    }

    protected function registerMiddleware()
    {
        // add the pre processing middleware
        $this->add(new OutputControl());
    }
}