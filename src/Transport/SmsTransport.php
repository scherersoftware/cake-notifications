<?php
namespace Notifications\Transport;

use App\Model\Entity\User;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\ORM\TableRegistry;
use Notifications\Model\Entity\Notification;
use Notifications\Model\Entity\NotificationContent;

require_once Plugin::path('Notifications') . 'src' . DS . 'Lib' . DS . 'WebSmsCom_Toolkit.inc';

class SmsTransport extends Transport {

	private $_smsClient = null;
/**
 * Creates a Transport instance
 *
 * @param array $config transport-specific configuration options
 */
    public function __construct(array $config) {
        parent::__construct($config);

        $keys = Configure::read('Notifications.transports.sms');
        $this->_smsClient = new \WebSmsCom_Client($keys['username'], $keys['password'], $keys['gateway']);
    }

/**
 * Abstract sender method
 *
 * @param User $user The recipient user
 * @param Notification $notification the notification to be sent
 * @param NotificationContent $content the content
 * @return mixed
 */
	public function sendNotification(User $user, Notification $notification, NotificationContent $content) {
		$user = TableRegistry::get('Users')->getUser($user->id);

		if (!empty($user->user_profile->phone)) {
        	$maxSmsPerMessage = isset($notification->transport_config['maxSmsPerMessage']) ? $notification->transport_config['maxSmsPerMessage'] : $this->_config['defaultMaxSmsPerMessage'];
			$test = Configure::read('debug');
			$text = $content->render('sms', $notification);
			$message  = new \WebSmsCom_TextMessage([$user->user_profile->phone], $text);

			if (empty($text)) {
				return false;
			}

			try {
				$response = $this->_smsClient->send($message, $maxSmsPerMessage, $test);
			} catch (\WebSmsCom_ParameterValidationException $e) {
			   echo("ParameterValidationException caught: ".$e->getMessage()."\n");
			   return false;
			} catch (\WebSmsCom_AuthorizationFailedException $e) {
				echo("AuthorizationFailedException caught: ".$e->getMessage()."\n");
				return false;
			} catch (\WebSmsCom_ApiException $e) {
				#echo $e; // possibility to handle API status codes $e->getCode()
				echo("ApiException Exception: " . $e->getCode() . "\n");
				return false;
			} catch (\WebSmsCom_HttpConnectionException $e) {
				echo("HttpConnectionException caught: ".$e->getMessage()."HTTP Status: ".$e->getCode()."\n");
				return false;
			} catch (\WebSmsCom_UnknownResponseException $e) {
				echo("UnknownResponseException caught: ".$e->getMessage()."\n");
				return false;
			} catch (\Exception $e) {
				echo("Exception caught: ".$e->getMessage()."\n");
				return false;
			}
			return $response->getStatusCode() === 2000;
		}
		
	}
}
