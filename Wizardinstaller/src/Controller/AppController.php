<?php

namespace Wizardinstaller\Controller;

use App\Controller\AppController as BaseController;
use Cake\Event\Event;

/**
 * Class AppController
 * @package Wizardinstaller\Controller
 */
class AppController extends BaseController
{
    public function initialize()
    {
        $this->loadComponent('Flash');
        $this->loadComponent('RequestHandler');
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        if (!array_key_exists('_serialize', $this->viewVars) && in_array($this->response->type(), ['application/json',
                                                                                                   'application/xml'])
        ) {
            $this->set('_serialize', TRUE);
        }
        $this->viewBuilder()
             ->layout('Wizardinstaller.default');
    }

}
