<?php
namespace Notifications\Controller;

use App\Model\Entity\User;
use Cake\Core\Configure;
use Notifications\Controller\AppController;

/**
 * UserNotifications Controller
 */
class UserNotificationsController extends AppController
{

    /**
     * @var array
     */
    public $components = ['RequestHandler'];

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
            $this->Flash->set(__d('notifications', 'error'));
            return $this->redirect($this->referer());
        }

        if ($this->NotificationQueue->read($id)) {
            // TODO actually business logic and therefor out of place in this plugin
            // TODO should be hasUserRight('viewCompleteProject') but AuthComponent has no
            // access to userrights right now, so:
            // if not a internal employee redirect to normal view of the linked entity, not
            // the deep link, which normal users don't have access to
            if (!in_array($this->Auth->user('role'), [User::ROLE_ADMIN, User::ROLE_USER])
                && !empty($notification->config['model'])
                && !empty($notification->config['foreign_key'])) {
                return $this->redirect([
                    'plugin' => false,
                    'controller' => $notification->config['model'],
                    'action' => 'view',
                    $notification->config['foreign_key']
                ]);
            }
            if (!empty($notification->config['redirect_link'])) {
                return $this->redirect($notification->config['redirect_link']);
            }
        } else {
            $this->Flash->error(__d('notifications', 'error'));
        }
        return $this->redirect($this->referer());
    }

    public function readAll($ids = [])
    {
        // Force a JSON response regardless of extension
        $this->RequestHandler->renderAs($this, 'json');
        $code = 'success';
        if (!empty($ids)) {
            foreach ($ids as $id) {
                if (!$this->NotificationQueue->read($id)) {
                    $code = 'error';
                }
            }
        }
        $this->set(compact('code'));
        $this->set('_serialize', ['code']);
    }

    public function mine()
    {
        // Force a JSON response regardless of extension
        $this->RequestHandler->renderAs($this, 'json');
        
        $userId = $this->Auth->user('id');
        $unreadNotifications = $this->NotificationQueue->getOnpageNotificationsForUser($userId, true, false, 50, 1);
        unset($unreadNotifications['moreEntriesAvailable']);
        
        $code = 'success';
        $this->set(compact('code', 'unreadNotifications'));
        $this->set('_serialize', ['code', 'unreadNotifications']);
    }
}
