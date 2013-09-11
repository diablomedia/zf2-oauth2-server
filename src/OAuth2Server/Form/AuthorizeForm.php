<?php
namespace OAuth2Server\Form;

use Zend\Form\Form;
use Zend\Form\Element;

class AuthorizeForm extends Form
{
    public function __construct($name, $csrfSalt)
    {
        parent::__construct($name);

        $this->add(
            new Element\Csrf('security', array('salt' => $csrfSalt))
        );

        $this->add(
            array(
                'name' => 'client_id',
                'attributes' => array(
                    'type' => 'hidden'
                )
            )
        );
        $this->add(
            array(
                'name' => 'redirect_uri',
                'attributes' => array(
                    'type' => 'hidden'
                )
            )
        );
        $this->add(
            array(
                'name' => 'response_type',
                'attributes' => array(
                    'type' => 'hidden'
                )
            )
        );
        $this->add(
            array(
                'name' => 'state',
                'attributes' => array(
                    'type' => 'hidden'
                )
            )
        );
        $this->add(
            array(
                'name' => 'scope',
                'attributes' => array(
                    'type' => 'hidden'
                )
            )
        );
        $this->add(
            array(
                'name' => 'authorize',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Authorize',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'deny',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Deny',
                ),
            )
        );

        $this->setAttribute('method', 'get'); // The bshaffer library currently does not allow POST here
    }
}
