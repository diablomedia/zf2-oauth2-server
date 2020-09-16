<?php
namespace OAuth2Server\Form;

use Laminas\Form\Form;
use Laminas\Form\Element;

class AuthorizeForm extends Form
{
    /**
     * @param string $name
     * @param string $csrfSalt
     */
    public function __construct($name, $csrfSalt)
    {
        parent::__construct($name);

        $this->add(
            new Element\Csrf('security', ['salt' => $csrfSalt])
        );

        $this->add(
            [
                'name'       => 'client_id',
                'attributes' => [
                    'type' => 'hidden'
                ]
            ]
        );
        $this->add(
            [
                'name'       => 'redirect_uri',
                'attributes' => [
                    'type' => 'hidden'
                ]
            ]
        );
        $this->add(
            [
                'name'       => 'response_type',
                'attributes' => [
                    'type' => 'hidden'
                ]
            ]
        );
        $this->add(
            [
                'name'       => 'state',
                'attributes' => [
                    'type' => 'hidden'
                ]
            ]
        );
        $this->add(
            [
                'name'       => 'scope',
                'attributes' => [
                    'type' => 'hidden'
                ]
            ]
        );
        $this->add(
            [
                'name'       => 'authorize',
                'attributes' => [
                    'type'  => 'submit',
                    'class' => 'btn btn-primary',
                    'value' => 'Authorize',
                ],
            ]
        );

        $this->add(
            [
                'name'       => 'deny',
                'attributes' => [
                    'type'  => 'submit',
                    'class' => 'btn btn-danger',
                    'value' => 'Deny',
                ],
            ]
        );

        $this->setAttribute('method', 'get'); // The bshaffer library currently does not allow POST here
    }
}
