<?php
/**
 * @author Stephen "TheCodeAssassin" Hoogendijk <admin@tca0.nl>
 */

namespace OpenTournament\Controller;

use OpenTournament\Api\Cas\Client;
use OpenTournament\Api\Exception as ApiException;
use OpenTournament\Controller;

/**
 * Class VirtualMachineController
 *
 * @package OpenTournament\Controller
 */
class VirtualMachinesController extends Controller
{
    /**
     * Lists VMs
     *
     * @Route('/v1/:product(/:casId)')
     *
     * @Method('GET')
     * @Name('virtual_machines.index')
     * @param string $product
     *
     * @param null|string   $casId
     *
     * @throws \OpenTournament\Api\Cas\Exception
     * @internal param string $platform
     */
    public function indexAction($product, $casId = null)
    {

        $page = $this->app->request->get('page');
        $offset = $this->app->request->get('offset', 50);

        $realProduct = null;
        if ($product == 'virtualServers') {
            $realProduct = Client::CLOUD_PRODUCT_DA;
        } elseif ($product == 'cloudInstances') {
            $realProduct = Client::CLOUD_PRODUCT_VR;
        }

        $casApi = $this->getCasApiClient();

        // todo get the customer number from the header
        $result = $casApi->getCloudCustomerStock('1301178860', $realProduct, $page, $offset);

        if ($casId !== null) {
            foreach ($result['Items'] as $item) {
                if ($item['Id'] == $casId) {
                    $result = array('Items' => array($item));
                }
            }
        }


        $this->outputJson($result);
    }

    /**
     * Reboots a VM with a given name
     *
     * @param string $casId CAS Resource ID
     *
     * @Route('/v1/virtualServers/:casId/reboot')
     * @Route('/v1/cloudInstances/:casId/reboot')
     *
     * @Method('POST')
     * @Name('virtual_machines.reboot')
     *
     * @throws ApiException
     * @throws \Exception
     */
    public function rebootAction($casId)
    {
        $this->executeCommand('reboot', $casId);
    }

    /**
     * Starts a VM with a given name
     *
     * @param string $casId CAS Resource ID
     *
     * @Route('/v1/virtualServers/:casId/powerOn')
     * @Route('/v1/cloudInstances/:casId/powerOn')
     *
     * @Method('POST')
     * @Name('virtual_machines.powerOn')
     *
     * @throws ApiException
     * @throws \Exception
     */
    public function powerOnAction($casId)
    {
        $this->executeCommand('powerOn', $casId);
    }

    /**
     * Stops a VM with a given name
     *
     * @param string $casId CAS Resource ID
     *
     * @Route('/v1/virtualServers/:casId/powerOff')
     * @Route('/v1/cloudInstances/:casId/powerOff')
     *
     * @Method('POST')
     * @Name('virtual_machines.powerOff')
     *
     * @throws ApiException
     * @throws \Exception
     */
    public function powerOffAction($casId)
    {
        $this->executeCommand('powerOff', $casId);
    }

    /**
     * Performs a command (powerOn, powerOff, reboot) on a VM with a given name
     *
     * @param string $command            powerOn|powerOff|reboot
     * @param string $platform           Platform identifier (cs01, cs07, ...)
     * @param string $virtualMachineName VM Name
     *
     * @throws ApiException
     * @throws \Exception
     */
    protected function executeCommand($command, $casId)
    {
        if (!in_array($command, array('powerOn', 'powerOff', 'reboot'))) {
            throw new ApiException(sprintf('%s is not a valid command for a VirtualMachine resource', $command));
        }

        $log = $this->openLog($command);

        $result = $casApi->getCloudCustomerStock('1301178860', $realProduct);

        $baseUrl = $this->getEcsApiBaseUrlByPlatform($platform);

        $parameters = array(
            'command' => $command.'Extended',
            'name' => $virtualMachineName,
        );

        try {
            $log->info(sprintf('[%s] Instance %s on platform %s', $command, $virtualMachineName, $platform));

            // call the reboot api method
            $this->ecsapiClient->call($baseUrl, $parameters);
        } catch (ApiException $e) {
            $log->critical(sprintf('[%s] %s', $command, $e->getMessage()));

            // todo should we even re-throw it?
            throw $e;
        }
    }
}