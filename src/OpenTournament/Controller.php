<?php
/**
 * @author Stephen "TheCodeAssassin" Hoogendijk <admin@tca0.nl>
 */

namespace OpenTournament;

use Guzzle\Http\Client;
use OpenTournament\Api\Cas\Client as CasClient;
use OpenTournament\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Slim;
use Flynsarmy\SlimMonolog\Log\MonologWriter;
use Monolog\Handler\StreamHandler;


/**
 * Class BaseController
 *
 * @package OpenTournament
 */
abstract class Controller
{

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var App
     */
    protected $app;

    /**
     * ecsApi Client
     *
     * @var Client
     */
    protected $ecsapiClient;

    /**
     * @var array
     */
    protected $parameters;

    const ECSAPI_URL = 'extended_api_url';

    /**
     * Base controller constructor
     */
    public function __construct()
    {
        $this->app = self::getApp();
        $this->response = $this->app->response;
        $this->request = $this->app->request;
        $this->ecsapiClient = $this->app->ecsapiClient;
        $this->parameters = $this->app->getParameters();
    }

    /**
     * Outputs Json response
     *
     * @param array $data Array data to output as JSON
     */
    public function outputJson($data)
    {
        $this->response->body(json_encode($data));
    }

    /**
     * Returns an array of platforms
     *
     * @return array
     */
    public function getPlatforms()
    {
        return $this->parameters['platforms'];
    }

    /**
     * Retrieve the parameters for a specific platform
     *
     * @param null $platform Optional platform to filter by
     *
     * @return array|bool
     */
    public function getParametersByPlatform($platform)
    {
        return (isset($this->parameters['platforms'][$platform]) ? $this->parameters['platforms'][$platform] : false);
    }

    /**
     * Get the ecsapi baseUrl by platform
     *
     * @param string $platform Platform name
     *
     * @return mixed
     */
    public function getEcsApiBaseUrlByPlatform($platform)
    {
        $parameters = $this->getParametersByPlatform($platform);

        return $parameters[self::ECSAPI_URL];
    }

    /**
     * @param string $product Product [DA/VR]
     *
     * @return array
     */
    public function getDefaultPlatformByProduct($product)
    {
        $platforms = $this->getPlatforms();
        $return = array();

        foreach ($platforms as $platform => $parameters) {
            if ($parameters['default_platform'] == 1 && $parameters['product'] == $product) {
                $return = array($platform => $parameters);
                break;
            }
        }

        return $return;
    }

    /**
     * Get the application's kernel (Slim object)
     *
     * @return App
     */
    protected function getApp($name = 'default')
    {
        return Slim::getInstance($name);
    }

    /**
     * Returns an instance of Guzzle HTTP Client
     *
     * @return Client
     */
    protected function getGuzzleClient()
    {
        return $this->app->guzzleClient;
    }

    /**
     * Returns an instance of CAS Api Client
     *
     * @return CasClient
     */
    protected function getCasApiClient()
    {
        return $this->app->casClient;
    }

    /**
     * Create a log channel
     *
     * @param string $name Log file name (without .log extension)
     *
     * @return \Slim\Log
     */
    protected function openLog($name)
    {
        $filename =  LOG_DIR . '/' . $name . '.log';

        $logWriter = new MonologWriter(array('handlers' => array(new StreamHandler($filename)), 'name' => 'LswCapiLogger'));

        $log = $this->app->getLog();
        $log->setWriter($logWriter);

        return $log;
    }

}