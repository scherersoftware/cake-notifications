<?php
namespace Notifications\Controller;

use Cake\Core\Configure;
use Notifications\Controller\AppController;

/**
 * UserNotifications Controller
 */
class UserNotificationsController extends AppController
{
    /**
     * initialize method
     *
     * @return void
     */
    public function initialize()
    {
        $this->loadModel('Notifications.NotificationQueue');
    }

    /**
     * Event
     *
     * @param Event $event event
     * @return void
     */
    public function beforeFilter(\Cake\Event\Event $event) {
        $this->loadModel('Notifications.NotificationContents');
        if (Configure::check('Notifications.default_language')) {
            $this->NotificationContents->locale(Configure::read('Notifications.default_language'));
        } else {
            $this->NotificationContents->locale('eng');
        }
        parent::beforeFilter($event);
    }

    /**
     * ajax action endpoint to render paginated list of unread user notifications
     *
     * @param  int    $page page number
     * @return void
     */
    public function renderUserNotificationBox($page = 1)
    {
        $userId = $this->Auth->user('id');
        $unreadNotifications = $this->NotificationQueue->getOnpageNotificationsForUser($userId, true, false, 5, $page);
        $moreEntriesAvailable = $unreadNotifications['moreEntriesAvailable'];
        // unset so it doesn't disturb the foreach over all notifications in the element
        unset($unreadNotifications['moreEntriesAvailable']);
        $excludeActionWrapper = true;
        $excludeMessageListWrapper = ($page > 1) ? true : false;

        $this->layout = false;
        $this->set(compact('excludeMessageListWrapper', 'unreadNotifications', 'excludeActionWrapper', 'moreEntriesAvailable', 'page'));
        return $this->render('./Element/user_notification_list');
    }

    /**
     * method to set the seen status of an user notification and redirect to its configured action
     *
     * @param  uuid $id identifier of the user notification
     * @return void
     */
    public function read($id = null)
    {
        $notification = $this->NotificationQueue->get($id);
        if (empty($notification)) {
            // bad
        }

        if ($this->NotificationQueue->read($id)) {
            if (!empty($notification->config['link'])) {
                $this->redirect($notification->config['link']);
            } else {
                // what now?
            }
        }
    }
}
