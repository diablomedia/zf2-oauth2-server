<?php

namespace OAuth2Server\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use OAuth2\ZendHttpPhpEnvironmentBridge\Request;
use OAuth2\Server;

class AuthorizeController extends AbstractActionController
{
    protected $userId;
    protected $preAuthorized = false;

    private $server;
    private $authorizeForm;

    public function __construct(Server $server, $authorizeForm)
    {
        $this->server = $server;
        $this->authorizeForm = $authorizeForm;
    }

    public function authorizeAction()
    {
        $this->getEventManager()->trigger('authorize.pre', $this);

        $request = Request::createFromRequest($this->getRequest());
        $response = $this->getResponse();

        if ($this->preAuthorized) {
            $isAuthorized = true;
        } else {
            $this->authorizeForm->setData($request->getQuery());
            if ($request->getQuery('authorize') && $this->authorizeForm->isValid()) {
                $isAuthorized = true;
            } elseif ($request->getQuery('deny') && $this->authorizeForm->isValid()) {
                $isAuthorized = false;
            }
        }
        if (isset($isAuthorized)) {
            $this->getEventManager()->trigger(
                'authorize.preHandle',
                $this,
                ['isAuthorized' => $isAuthorized, 'preAuthorized' => $this->preAuthorized]
            );

            $response = $this->server->handleAuthorizeRequest(
                $request,
                $this->getResponse(),
                $isAuthorized,
                $this->userId
            );
            $response->sendHeaders();
            return new JsonModel($response->getContent());
        }

        if (!$this->server->validateAuthorizeRequest($request, $response)) {
            $headers = $response->getHeaders();
            $location = $headers->get('location');
            if ($location) {
                $headers->removeHeader($location);
            }
            return new JsonModel($response->getContent());
        }

        $this->authorizeForm->setData($request->getQuery());
        $client = $this->server->getStorage('client')->getClientDetails($request->getQuery('client_id'));

        $this->getEventManager()->trigger('authorize.post', $this);

        return ['form' => $this->authorizeForm, 'appname' => $client['name']];
    }

    public function tokenAction()
    {
        $request = Request::createFromRequest($this->getRequest());
        $response = $this->server->handleTokenRequest($request, $this->getResponse());

        return new JsonModel($response->getContent());
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setPreAuthorized($val)
    {
        $this->preAuthorized = $val;
    }

    public function getPreAuthorized()
    {
        return $this->preAuthorized;
    }
}
