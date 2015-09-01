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
        $view = $this->getView();
        $view->layout = Configure::read('Notifications.Administration.layout');
        foreach (Configure::read('Notifications.Administration.helpers') as $helper) {
            $view->loadHelper($helper);
        }
        parent::beforeRender($event);
    }
}
