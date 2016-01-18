<?php
namespace Notifications\Controller;

use Cake\Core\Configure;
use Notifications\Controller\AppController;

/**
 * NotificationContents Controller
 *
 * @property Notifications\Model\Table\NotificationContentsTable $NotificationContents
 */
class NotificationQueueController extends AppController {

    public $listFilters = [
        'index' => [
            'fields' => [
                'RecipientUsers.email' => [
                    'type' => 'wildcard',
                    'label' => 'Email'
                ],
                'RecipientUsers.phone' => [
                    'type' => 'wildcard',
                    'label' => 'Phone'
                ]
            ]
        ]
    ];

/**
 * Event
 *
 * @param Event $event event
 * @return void
 */
    public function beforeFilter(\Cake\Event\Event $event) {
        $this->loadModel('Notifications.NotificationQueue');
        $this->loadModel('Notifications.NotificationContents');
        $this->loadModel('Users');
        parent::beforeFilter($event);
    }


/**
 * Index method
 *
 * @return void
 */
    public function index() {
        $this->paginate['contain'] = ['RecipientUsers'];
        $this->paginate['order'] = ['NotificationQueue.created' => 'desc'];
        $notificationQueues = $this->paginate($this->NotificationQueue)->toArray();

        // get a preview of the notification
        foreach ($notificationQueues as $key => $notificationQueue) {
            $this->NotificationContents->locale($notificationQueue->locale);
            $notificationContent = $this->NotificationContents->getByIdentifier($notificationQueue->notification_identifier);
            switch ($notificationQueue->transport) {
                case 'email':
                    $content = '<b>email_subject: ';
                    $content .= $notificationContent->render('email_subject', $notificationQueue);
                    $content .= '</b><br>email_html: ';
                    $content .= $notificationContent->render('email_html', $notificationQueue);
                    $content .= '<br>email_text: ';
                    $content .= $notificationContent->render('email_text', $notificationQueue);
                    break;
                case 'onpage':
                    $content = $notificationContent->render('onpage', $notificationQueue);
                    $content .= '<br>';
                    $content .= 'Link: <a href="' . $notificationContent->render('onpage_link', $notificationQueue) . '">';
                    $content .= $notificationContent->render('onpage_link_title', $notificationQueue);
                    $content .= '</a>';
                    break;

                default:
                    $content = $notificationContent->render($notificationQueue->transport, $notificationQueue);
                    break;
            }
            if (empty($content)) {
                $content = __d('notifications', 'no_such_transport_content_or_translation');
            }
            $notificationQueues[$key]->notification_content = $content;
        }

        $this->set('notificationQueues', $notificationQueues);
    }

/**
 * resend method
 *
 * @param string $id content id
 * @return void
 * @throws \Cake\Network\Exception\NotFoundException
 */
    public function resend($id = null) {
        $queue = $this->NotificationQueue->get($id);
        $queue->set([
            'sent' => false,
            'locked' => false,
            'send_tries' => 0
        ]);

        if ($this->NotificationQueue->save($queue)) {
            $this->Flash->success(__d('notifications', 'notification_content.crud.save_successful'));
        } else {
            $this->Flash->error(__d('notifications', 'notification_content.crud.validation_failed'));
        }

        return $this->redirect([
            'action' => 'index'
        ]);
    }
}
