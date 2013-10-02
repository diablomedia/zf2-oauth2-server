<?php

namespace OAuth2Server\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use OAuth2\ZendHttpPhpEnvironmentBridge\Request;

class AuthorizeController extends AbstractActionController
{
    protected $userId;
    protected $preAuthorized = false;

    public function authorizeAction()
    {
        $this->getEventManager()->trigger('authorize.pre', $this);

        $serviceManager = $this->getServiceLocator();
        $request = Request::createFromRequest($this->getRequest());
        $response = $this->getResponse();
        $server = $serviceManager->get('OAuth2Server\Server');

        if ($this->preAuthorized) {
            $isAuthorized = true;
        } else {
            $form = $serviceManager->get('OAuth2Server\AuthorizeForm');
            $form->setData($request->getQuery());
            if ($request->getQuery('authorize') && $form->isValid()) {
                    $isAuthorized = true;
            } elseif ($request->getQuery('deny') && $form->isValid()) {
                    $isAuthorized = false;
            }
        }

        if (isset($isAuthorized)) {
            $this->getEventManager()->trigger('authorize.preHandle', $this, array('isAuthorized' => $isAuthorized, 'preAuthorized' => $this->preAuthorized));

            $response = $server->handleAuthorizeRequest($request, $this->getResponse(), $isAuthorized, $this->userId);
            $response->sendHeaders();
            exit;
        }

        if (!$server->validateAuthorizeRequest($request, $response)) {
            return new JsonModel($response->getContent());
        }

        $form->setData($request->getQuery());
        $client = $server->getStorage('client')->getClientDetails($request->getQuery('client_id'));

        $this->getEventManager()->trigger('authorize.post', $this);

        return array('form' => $form, 'appname' => $client['name']);
    }

    public function tokenAction()
    {
        $server = $this->getServiceLocator()->get('OAuth2Server\Server');
        $config = $this->getServiceLocator()->get('config')['oauth2server'];

        $request = Request::createFromRequest($this->getRequest());
        $response = $server->handleTokenRequest($request, $this->getResponse());

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
