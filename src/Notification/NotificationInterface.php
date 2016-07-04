<?php
namespace Notifications\Notification;

interface NotificationInterface
{

    /**
     * Get/Set Before send callback.
     *
     * @param array|null
     * @return array
    */
    public function beforeSendCallback();

    /**
     * Get/Set After send callback.
     *
     * @param array|null
     * @return array
    */
    public function afterSendCallback();

    /**
     * Get/Set queue options.
     *
     * @param array|null
     * @return array
    */
    public function queueOptions();

    /**
     * Push the notification into a queue
     *
     * @return bool
     */
    public function push();
}
