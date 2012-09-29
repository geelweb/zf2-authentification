<?php

namespace Authentication\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class AuthenticationPlugin extends AbstractPlugin
{
    protected $event;
    protected $authenticationService;

    public function setAuthenticationService($service)
    {
        $this->authenticationService = $service;
    }

    public function getAuthenticationService()
    {
        if (!$this->authenticationService) {
            $application = $this->event->getApplication();
            $sm = $application->getServiceManager();
            $this->authenticationService = $sm->get('AuthService');
        }

        return $this->authenticationService;
    }

    public function doAuthorization($event)
    {
        $this->event = $event;

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
    }
}
