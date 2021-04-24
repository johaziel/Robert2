<?php
declare(strict_types=1);

namespace Robert2\API\Controllers;

use DI\Container;
use Robert2\API\Config\Config;
use Robert2\API\Services\View;
use Slim\Http\Response;
use Slim\Http\ServerRequest as Request;

class EntryController extends BaseController
{
    /** @var View */
    private $view;

    public function __construct(Container $container, View $view)
    {
        parent::__construct($container);

        $this->view = $view;
    }

    public function index(Request $request, Response $response)
    {
        if (!Config::customConfigExists()) {
            return $response->withRedirect('/install', 302); // 302 Redirect
        }

        $serverConfig = $this->getServerConfig();
        return $this->view->render($response, 'webclient.twig', \compact('serverConfig'));
    }

    // ------------------------------------------------------
    // -
    // -    Internal methods
    // -
    // ------------------------------------------------------

    protected function getServerConfig(): callable
    {
        $rawConfig = Config::getSettings();
        $config = [
            'baseUrl' => $rawConfig['apiUrl'],
            'api' => [
                'url' => $rawConfig['apiUrl'] . '/api',
                'headers' => $rawConfig['apiHeaders'],
                'version' => Config::getVersion(),
            ],
            'auth' => [
                'cookie' => $rawConfig['auth']['cookie'],
                'timeout' => $rawConfig['sessionExpireHours'],
            ],
            'defaultPaginationLimit' => $rawConfig['maxItemsPerPage'],
            'defaultLang' => $rawConfig['defaultLang'],
            'currency' => $rawConfig['currency'],
            'beneficiaryTagName' => $rawConfig['defaultTags']['beneficiary'],
            'technicianTagName' => $rawConfig['defaultTags']['technician'],
            'billingMode' => $rawConfig['billingMode'],
            'degressiveRate' => sprintf(
                'function (daysCount) { return %s; }',
                $rawConfig['degressiveRateFunction']
            ),
        ];

        return function () use ($config): string {
            $jsonConfig = json_encode($config, Config::JSON_OPTIONS);
            $jsonConfig = preg_replace('/"degressiveRate": "/', '"degressiveRate": ', $jsonConfig);
            return preg_replace('/}"/', '}', $jsonConfig);
        };
    }
}
