<?php
namespace Notifications\Controller;

use Cake\Core\Configure;
use Notifications\Controller\AppController;

/**
 * NotificationContents Controller
 *
 * @property Notifications\Model\Table\NotificationContentsTable $NotificationContents
 */
class NotificationContentsController extends AppController {

/**
 * Event
 *
 * @param Event $event event
 * @return void
 */
    public function beforeFilter(\Cake\Event\Event $event) {
        $this->loadModel('Notifications.NotificationContents');
        parent::beforeFilter($event);
    }

/**
 * Index method
 *
 * @return void
 */
    public function index() {
        if (Configure::check('Notifications.default_language')) {
            $this->NotificationContents->locale(Configure::read('Notifications.default_language'));
        } else {
            $this->NotificationContents->locale('eng');
        }
        $this->set('notificationContents', $this->paginate($this->NotificationContents));
    }

/**
 * Add method
 *
 * @return void
 */
    public function add() {
        $notificationContent = $this->NotificationContents->newEntity();
        $transports = Configure::read('Notifications.transports');
        if ($this->request->is('post')) {
            $this->NotificationContents->patchEntity($notificationContent, $this->request->data);
            if ($this->NotificationContents->save($notificationContent)) {
                $this->Flash->success(__d('notifications', 'notification_content.crud.save_successful'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('notifications', 'notification_content.crud.validation_failed'));
            }
        }
        $this->set(compact('notificationContent', 'transports'));
        return $this->render('edit');
    }

/**
 * Edit method
 *
 * @param string $id content id
 * @return void
 * @throws \Cake\Network\Exception\NotFoundException
 */
    public function edit($id = null) {
        $supportedLanguages = Configure::read('Notifications.supported_languages');
        $translations = [];
        foreach ($supportedLanguages as $language) {
            $this->NotificationContents->locale($language);
            $notificationContent = $this->NotificationContents->get($id, [
                'locales' => $supportedLanguages
            ]);
            $translations[$language] = $notificationContent;
        }
        $transports = Configure::read('Notifications.transports');
        if ($this->request->is(['patch', 'post', 'put'])) {
            $currentLocale = $this->request->data['locale'];
            $this->NotificationContents->locale($currentLocale);
            $notificationContent = $translations[$currentLocale];
            $translations[$currentLocale] = $this->NotificationContents->patchEntity($translations[$currentLocale], $this->request->data);
            if ($this->NotificationContents->save($translations[$this->request->data['locale']])) {
                $this->Flash->success(__d('notifications', 'notification_content.crud.save_successful'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('notifications', 'notification_content.crud.validation_failed'));
            }
        }
        if (empty($this->request->data['locale'])) {
            $this->request->data['locale'] = Configure::read('Notifications.default_language');
        }
        $this->set(compact('translations', 'transports', 'supportedLanguages'));
    }

}
