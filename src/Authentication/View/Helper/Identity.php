<?php

namespace Authentication\View\Helper;

use Zend\Authentication\AuthenticationService,
    Zend\View\Helper\AbstractHelper,
    Zend\View\Exception;

class Identity extends AbstractHelper
{
    protected $authenticationService;

    public function getAuthenticationService()
    {
        return $this->authenticationService;
    }

    public function setAuthenticationService(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
        return $this;
    }

    public function __invoke()
    {
        $service = $this->getAuthenticationService();
        if (! ($service instanceof AuthenticationService)) {
            return null;
            throw new Exception\RuntimeException('No AuthenticationService instance provided');
        }
        if ($service->hasIdentity()) {
            return $service->getIdentity();
        }
        return null;
    }
}
