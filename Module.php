<?php

namespace Authentication;

use Zend\Mvc\MvcEvent,
    Zend\Authentication\Adapter\DbTable as AuthAdapterDbTable,
    Zend\Authentication\AuthenticationService;

use Authentication\Model\UserTable;

class Module
{
    /**
     * Gets the autoloader config
     *
     * @access public
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * Gets the module config defined in the /config/module.config.php file
     *
     * @access public
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Authentication\Model\UserTable' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new UserTable($dbAdapter);
                    return $table;
                },
                'AuthAdapter' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $authAdapter = new AuthAdapterDbTable($dbAdapter,
                                                          'auth_user',
                                                          'username',
                                                          'password',
                                                          'crypt(?, password)');
                    return $authAdapter;
                },
            ),
            'invokables' => array(
                'AuthService' => 'Zend\Authentication\AuthenticationService',
            ),
        );
    }

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()
                          ->getEventManager()
                          ->attach('dispatch', array($this, 'loadConfiguration'), 2);

    }

    public function loadConfiguration(MvcEvent $e)
    {
        $application = $e->getApplication();
        $sm = $application->getServiceManager();
        $sharedManager = $application->getEventManager()->getSharedManager();

        $sharedManager->attach(
            'Zend\Mvc\Controller\AbstractActionController',
            'dispatch',
            function($e) use ($sm) {
                $sm->get('ControllerPluginManager')
                    ->get('AuthenticationPlugin')
                    ->doAuthorization($e);
            }
        );

    }

}
