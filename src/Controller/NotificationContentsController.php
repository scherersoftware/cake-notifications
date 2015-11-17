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
        $supportedLanguages = Configure::read('Notifications.supported_languages');
        $defaultLanguage = Configure::read('Notifications.default_language');;
        $translations = [];
        foreach ($supportedLanguages as $locale => $languageDescription) {
            $this->NotificationContents->locale($locale);
            $notificationContent = $this->NotificationContents->newEntity();
            $translations[$locale] = $notificationContent;
        }
        $transports = Configure::read('Notifications.transports');

        if ($this->request->is('post')) {
            $currentLocale = empty($this->request->data['locale']) ? $defaultLanguage : $this->request->data['locale'];
            $this->NotificationContents->locale($currentLocale);
            $notificationContent = $this->NotificationContents->patchEntity($translations[$currentLocale], $this->request->data);
            if ($this->NotificationContents->save($notificationContent)) {
                $this->Flash->success(__d('notifications', 'notification_content.crud.save_successful'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('notifications', 'notification_content.crud.validation_failed'));
            }
        }
        if (empty($this->request->data['locale'])) {
            $this->request->data['locale'] = $defaultLanguage;
        }
        $this->set(compact('translations', 'transports', 'supportedLanguages'));
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
        $defaultLanguage = Configure::read('Notifications.default_language');;
        $translations = [];
        foreach ($supportedLanguages as $locale => $languageDescription) {
            $this->NotificationContents->locale($locale);
            $notificationContent = $this->NotificationContents->get($id, [
                'locales' => $supportedLanguages
            ]);
            $translations[$locale] = $notificationContent;
        }
        $transports = Configure::read('Notifications.transports');
        if ($this->request->is(['patch', 'post', 'put'])) {
            $currentLocale = empty($this->request->data['locale']) ? $defaultLanguage : $this->request->data['locale'];
            $this->NotificationContents->locale($currentLocale);
            $notificationContent = $this->NotificationContents->patchEntity($translations[$currentLocale], $this->request->data);
            if ($this->NotificationContents->save($notificationContent)) {
                $this->Flash->success(__d('notifications', 'notification_content.crud.save_successful'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__d('notifications', 'notification_content.crud.validation_failed'));
            }
        }
        if (empty($this->request->data['locale'])) {
            $this->request->data['locale'] = $defaultLanguage;
        }
        $this->set(compact('translations', 'transports', 'supportedLanguages'));
    }

}
