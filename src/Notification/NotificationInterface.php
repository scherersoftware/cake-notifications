<?php
namespace Notifications\Notification;

interface NotificationInterface
{

    public function beforeSendCallback();

    public function afterSendCallback();

    public function queue();

    public function push();

    public function reset();

}
