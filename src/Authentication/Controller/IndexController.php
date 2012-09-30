<?php

namespace Authentication\Controller;

use Zend\Mvc\Controller\AbstractActionController,
    Zend\View\Model\ViewModel;

use Authentication\Form\LoginForm;

class IndexController extends AbstractActionController
{
    public function loginAction()
    {
        $form = new LoginForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $sm = $this->getServiceLocator();
                $data = $form->getData();

                $adapter = $sm->get('AuthAdapter');
                $adapter->setIdentity($data['username']);
                $adapter->setCredential($data['password']);

                $service = $sm->get('AuthService');
                $result = $service->authenticate($adapter);
                if ($result->isValid()) {
                    $storage = $service->getStorage();
                    $storage->write($adapter->getResultRowObject(
                        null,
                        'password'));

                    // TODO make this route customizable
                    return $this->redirect()->toRoute('home');
                }

                return array(
                    'form' => $form,
                    'error_message' => array_pop($result->getMessages()),
                );
            }
        }

        return array('form' => $form);
    }

    public function logoutAction()
    {
        $sm = $this->getServiceLocator();
        $service = $sm->get('AuthService');
        $service->clearIdentity();

        // TODO make this route customizable
        return $this->redirect()->toRoute('home');
    }
}
