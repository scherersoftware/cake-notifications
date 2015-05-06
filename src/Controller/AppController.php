<?php

namespace Notifications\Controller;

use \App\Controller\AppController as BaseController;

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
}
