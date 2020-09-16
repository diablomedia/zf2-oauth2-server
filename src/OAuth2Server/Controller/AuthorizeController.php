<?php

namespace OAuth2Server\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use OAuth2\LaminasHttpPhpEnvironmentBridge\Request;
use OAuth2\Server;

class AuthorizeController extends AbstractActionController
{
    /**
     * @var mixed
     */
    protected $userId;

    /**
     * @var bool
     */
    protected $preAuthorized = false;

    /**
     * @var Server
     */
    private $server;

    /**
     * @var \OAuth2Server\Form\AuthorizeForm
     */
    private $authorizeForm;

    /**
     * @param \OAuth2Server\Form\AuthorizeForm $authorizeForm
     */
    public function __construct(Server $server, $authorizeForm)
    {
        $this->server        = $server;
        $this->authorizeForm = $authorizeForm;
    }

    /**
     * @return array|JsonModel
     */
    public function authorizeAction()
    {
        $this->getEventManager()->trigger('authorize.pre', $this);

        $request  = Request::createFromRequest($this->getRequest());
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
            $headers  = $response->getHeaders();
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

    /**
     * @return JsonModel
     */
    public function tokenAction()
    {
        $request  = Request::createFromRequest($this->getRequest());
        $response = $this->server->handleTokenRequest($request, $this->getResponse());

        return new JsonModel($response->getContent());
    }

    /**
     * @param mixed $userId
     * @return void
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param bool $val
     * @return void
     */
    public function setPreAuthorized($val)
    {
        $this->preAuthorized = $val;
    }

    /**
     * @return bool
     */
    public function getPreAuthorized()
    {
        return $this->preAuthorized;
    }
}
