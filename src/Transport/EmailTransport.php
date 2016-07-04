<?php
namespace Notifications\Transport;

use Cake\Mailer\Email;

class EmailTransport extends Transport
{

    /**
     * Send function
     *
     * @param obj $job
     * @return void
     */
    public function sendNotification($job)
    {
        $beforeSendCallback = $job->data('beforeSendCallback');
        $this->_performCallback($beforeSendCallback);

        $email = new Email();
        $email->unserialize($job->data('email'));
        $email->send();

        $afterSendCallback = $job->data('afterSendCallback');
        $this->_performCallback($afterSendCallback);
    }
}
