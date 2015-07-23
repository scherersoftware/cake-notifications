<?php
namespace Notifications\View\Helper;

// use Cake\Collection\Collection;
use Cake\Core\Configure;
// use Cake\Core\Exception\Exception;
// use Cake\Form\Form;
// use Cake\ORM\Entity;
// use Cake\Routing\Router;
// use Cake\Utility\Hash;
// use Cake\Utility\Inflector;
// use Cake\Utility\Security;
// use Cake\View\Form\ArrayContext;
// use Cake\View\Form\ContextInterface;
// use Cake\View\Form\EntityContext;
// use Cake\View\Form\FormContext;
// use Cake\View\Form\NullContext;
use Cake\View\Helper;
// use Cake\View\Helper\IdGeneratorTrait;
// use Cake\View\StringTemplateTrait;
// use Cake\View\View;
// use Cake\View\Widget\WidgetRegistry;
// use DateTime;
// use RuntimeException;
// use Traversable;

/**
 * UserNotification helper
 */
class UserNotificationHelper extends Helper
{
    /**
     * Other helpers used
     *
     * @var array
     */
    public $helpers = ['Html', 'Auth'];

    public function renderUserNotification($notification)
    {
        $checkUrl = [
            'plugin' => false,
            'controller' => $notification->config['model'],
            'action' => 'view',
            $notification->config['foreign_key']
        ];

        $string = '<div class="message">';
        if (Configure::read('Notifications.checkAuthenticationForLinks') && $this->Auth->urlAllowed($checkUrl)) {
            $string .= $this->Html->link($notification->content, [
                'plugin' => 'Notifications',
                'controller' => 'UserNotifications',
                'action' => 'read',
                $notification->id
            ], [
                'class' => 'message-subject'
            ]);
        } else {
            $string .= '<div class="message-subject">' . $notification->content . "</div>";
        }
        $string .= '<div class="message-description">';

        if (!empty($notification->config['responsible_user.full_name'])) {
            $string .= __d('notifications', 'from') . ' ' . $notification->config['responsible_user.full_name'];
            $string .= '&nbsp;&nbsp;·&nbsp;&nbsp;';
        }

        $string .= $notification->created->timeAgoInWords();
        $string .= '</div></div>';

        return $string;
    }
}
