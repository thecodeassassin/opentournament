<?php
/**
 * @author Stephen "TheCodeAssassin" Hoogendijk <admin@tca0.nl>
 */

namespace OpenTournament\Json;

use OpenTournament\App;

/**
 * Class Response
 *
 * @package Lsw\Response
 */
class Response
{

    /**
     * @var array data
     */
    protected $data = array();

    /**
     * @var App
     */
    protected $app;

    /**
     * @var int
     */
    protected $status = 503;

    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $resourceName name of the resource (such as dogs, cats, servers etc)
     * @param array  $data
     *
     * @return array
     */
    public function success($resourceName, array $data)
    {
        $this->status = 200;
        $this->data = array(
          $resourceName => $data
        );

        return $this->send();
    }

    /**
     *
     * Indicates a failed response
     *
     * @param string|int $errorCode
     * @param string     $errorMessage
     * @param null       $reference
     * @param string     $userMessage
     *
     * @todo are we going to log failure's?
     * @return array
     */
    public function failure($errorCode, $errorMessage, $reference = null, $userMessage = '')
    {
        $this->status = 500;

        $this->data = $this->errorResponse($errorCode, $errorMessage, $reference, $userMessage);

        return $this->send();
    }

    /**
     * @param string $resourceName
     * @param int    $errorCode
     *
     * @return array
     */
    public function notFound($resourceName = null, $errorCode = 404)
    {
        $this->status = 404;

        $message = ($resourceName
            ? sprintf('Resource \'%s\' was not found', $resourceName)
            : 'The requested resource was not found');

        $this->data = $this->errorResponse(
            $errorCode,
            $message,
            'https://www.leaseweb.com/contact'
        );

        return $this->send(false);
    }

    /**
     * @param \Exception $e
     *
     * @return array
     */
    public function exception(\Exception $e)
    {
        $this->status = 500;

        $this->data = $this->errorResponse(
            500,
            'A unknown exception occurred.'
        );

        // log the error
        $this->app->getLog()->critical($e);

        return $this->send();
    }

    /**
     * @param string|int $errorCode
     * @param string     $errorMessage
     * @param null       $reference
     * @param string     $userMessage
     *
     * @return array
     */
    protected function errorResponse($errorCode, $errorMessage, $reference = null, $userMessage = '')
    {
        return compact('errorCode', 'errorMessage', 'reference', 'userMessage');
    }

    /**
     * Send the response
     *
     * @param bool $writeToResponse
     *
     * @return array
     */
    protected function send($writeToResponse = true)
    {
        // set the correct HTTP status code
        $this->app->response->setStatus($this->status);


        // notFound doesn't support writing to the response object, see https://github.com/codeguy/Slim/issues/881
        if ($writeToResponse) {
            $this->app->response->write(json_encode($this->data));
        } else {
            echo json_encode($this->data);
        }

        $this->app->response->finalize();

        return $this->data;

    }
}