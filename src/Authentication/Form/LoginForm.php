<?php

namespace Authentication\Form;

use Zend\Form\Form;

class LoginForm extends Form
{
    public function __construct($name='login')
    {
        parent::__construct($name);
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'username',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Username',
            ),
        ));
        $this->add(array(
            'name' => 'password',
            'type' => 'Zend\Form\Element\Password',
            'options' => array(
                'label' => 'Password',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Sign-in',
                'id' => 'submitbutton',
            ),
        ));
    }
}
