<?php
namespace Notifications\Controller;

use Cake\Core\Configure;
use Notifications\Controller\AppController;

/**
 * UserNotifications Controller
 */
class UserNotificationsController extends AppController
{
    public function renderUserNotificationBox()
    {
        $userId = $this->Auth->user('id');
        $unreadNotifications = $this->UserNotifications->getNotificationsForUser($userId, true);
        $this->set(compact('unreadNotifications'));
        $this->layout = false;
    }
}
