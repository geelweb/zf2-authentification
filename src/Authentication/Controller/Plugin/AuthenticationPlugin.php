<?php

namespace Authentication\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin,
    Zend\Authentication\AuthenticationService;

class AuthenticationPlugin extends AbstractPlugin
{
    protected $authenticationService;

    public function setAuthenticationService($service)
    {
        $this->authenticationService = $service;
    }

    public function getAuthenticationService()
    {
        if (!$this->authenticationService) {
            $this->authenticationService = new AuthenticationService();
        }

        return $this->authenticationService;
    }

    public function doAuthorization($event)
    {
        $routeMatch = $event->getRouteMatch();
        $controller = $routeMatch->getParam('controller');
        $action     = $routeMatch->getParam('action');

        // prevent infinite loops
        if ($controller == 'Authentication\Controller\Index' && $action == 'login') {
            return;
        }

        $service = $this->getAuthenticationService();

        if (!$service->hasIdentity()) {
            $url = $event->getRouter()
                         ->assemble(array(), array('name' => 'login'));
            $response = $event->getResponse();
            $response->getHeaders()
                     ->addHeaderLine('Location', $url);
            $response->sendHeaders();
            exit;
        }

        $identity = $service->getIdentity();
        $require_super_user = $routeMatch->getParam('require_super_user', false);
        if ($require_super_user && !$identity->is_super_user) {
            $response = $event->getResponse();
            $response->setStatusCode(403);
            $response->sendHeaders();
            echo $response->getReasonPhrase();
            exit;
        }
    }

    public function hasIdentity()
    {
        return $this->getAuthenticationService()->hasIdentity();
    }

    public function getIdentity()
    {
        return $this->getAuthenticationService()->getIdentity();
    }
}
