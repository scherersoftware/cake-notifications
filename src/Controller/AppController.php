<?php

namespace Notifications\Controller;

use \App\Controller\AppController as BaseController;
use \Cake\Core\Configure;
use \Cake\Event\Event;

class AppController extends BaseController {
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * @return void
     */
    public function initialize()
    {
        $this->helpers[] = 'CkTools.CkTools';
        parent::initialize();
    }

    /**
     * beforeRender Event
     *
     * @param Event $event Event
     * @return void
     */
    public function beforeRender(Event $event)
    {
        // For good integration in existing administration areas, configure
        // View things here.
        if (method_exists($this, 'getView')) {
            // CakePHP 3.0.x
            $view = $this->getView();
            $view->layout = Configure::read('Notifications.Administration.layout');
            foreach (Configure::read('Notifications.Administration.helpers') as $helper) {
                $view->loadHelper($helper);
            }
        } else {
            // CakePHP 3.1.x
            $this->viewBuilder()->helpers(Configure::read('Notifications.Administration.helpers'));
            $this->viewBuilder()->layout(Configure::read('Notifications.Administration.layout'));
        }
        parent::beforeRender($event);
    }
}
