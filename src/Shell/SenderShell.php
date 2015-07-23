<?php
namespace Notifications\Shell;

use Cake\Console\Shell;

/**
 * Responsible for sending a batch from the NotificationQueue
 *
 * @package default
 */
class SenderShell extends Shell {

/**
 * Main
 *
 * @return void
 */
    public function main() {
        $this->loadModel('Notifications.NotificationQueue');
        $batch = $this->NotificationQueue->getBatch(10);
        if (!empty($batch)) {
            $batchIds = [];
            $sent = 0;
            $failed = 0;
            foreach ($batch as $notification) {
                $batchIds[] = $notification->id;
                if ($this->NotificationQueue->send($notification)) {
                    $this->NotificationQueue->success($notification->id);
                    $sent++;
                } else {
                    $this->NotificationQueue->fail($notification->id);
                    $failed++;
                }
            }
            $this->NotificationQueue->releaseLocks($batchIds);
            $this->out("Batch Size: " . count($batch) . " - Successes: {$sent} - Failures: {$failed}");
        } else {
            $this->out('Notification Queue Batch is empty.');
        }
    }
}
