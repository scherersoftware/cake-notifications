<?php
    $this->assign('title', __d('notifications', 'notification_queue.headline'));
?>

<h2 class="page-header">
    <?= __d('notifications', 'notification_queue.headline') ?>
    <div class="pull-right">
    </div>
</h2>

<?php echo $this->ListFilter->renderFilterbox($filters); ?>

<table class="table table-striped">
    <thead>
        <tr>
            <th><?= __d('notifications', 'notification_queue.recipient_user_fullname') ?></th>
            <th><?= $this->Paginator->sort('recipient_user.email', __d('notifications', 'notification_queue.recipient_user_email')) ?></th>
            <th><?= $this->Paginator->sort('recipient_user.phone', __d('notifications', 'notification_queue.recipient_user_phone')) ?></th>
            <th><?= $this->Paginator->sort('notification_identifier', __d('notifications', 'notification_queue.notification_identifier')) ?></th>
            <th><?= $this->Paginator->sort('transport', __d('notifications', 'notification_queue.notification_transport')) ?></th>
            <th><?= $this->Paginator->sort('locked', __d('notifications', 'notification_queue.locked')) ?></th>
            <th><?= $this->Paginator->sort('send_tries', __d('notifications', 'notification_queue.send_tries')) ?></th>
            <th><?= $this->Paginator->sort('sent', __d('notifications', 'notification_queue.sent')) ?></th>
            <th><?= $this->Paginator->sort('created', __d('notifications', 'notification_queue.created')) ?></th>
            <th><?= $this->Paginator->sort('modified', __d('notifications', 'notification_queue.modified')) ?></th>
            <th class="actions"><?= __d('notifications', 'notification_content.lists.actions') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($notificationQueues as $notificationQueue): ?>
        <tr>
            <td><?= $this->Html->link($notificationQueue->recipient_user->fullName(), [
                'plugin'=> 'AdminArea',
                'controller' => 'Users',
                'action' => 'view',
                $notificationQueue->recipient_user_id
            ]) ?></td>
            <td><?= h($notificationQueue->recipient_user->email) ?></td>
            <td><?= h($notificationQueue->recipient_user->phone) ?></td>

            <td><?= $this->Html->link($notificationQueue->notification_identifier, [
                'controller' => 'NotificationContents',
                'action' => 'edit',
                0,
                $notificationQueue->notification_identifier
            ]) ?></td>
            <td><?= h($notificationQueue->transport) ?></td>
            <td><?= $this->Utils->bool($notificationQueue->locked) ?></td>
            <td><?= ($notificationQueue->send_tries) ?></td>
            <td><?= $this->Utils->bool($notificationQueue->sent) ?></td>
            <td><?= h($this->Utils->dateTime($notificationQueue->created)) ?></td>
            <td><?= h($this->Utils->dateTime($notificationQueue->modified)) ?></td>
            <td class="actions text-center">
                <?= $this->Html->link('<span class="fa fa-search"></span><span class="sr-only">' . __d('notifications', 'notification_queue.view_content') . '</span>',
                '#collapse-notification-content-' . $notificationQueue->id,
                [
                    'escape' => false,
                    'role' => 'button',
                    'data-toggle' => 'collapse',
                    'aria-expanded' => false,
                    'aria-controls' => '#collapse-notification-content-' . $notificationQueue->id,
                    'class' => 'btn btn-xs btn-default',
                    'title' => __d('notifications', 'notification_queue.view_content')
                ]) ?>
                <?= $this->Html->link('<span class="fa fa-share"></span><span class="sr-only">' . __d('notifications', 'notification_queue.resend') . '</span>', [
                    'action' => 'resend',
                    $notificationQueue->id
                ], [
                    'escape' => false,
                    'class' => 'btn btn-xs btn-default',
                    'title' => __d('notifications', 'notification_queue.resend')
                ]) ?>
            </td>
        </tr>
        <tr class="collapse" id="collapse-notification-content-<?= $notificationQueue->id ?>">
            <td colspan="11" style="border-top: none;">
                <div class="well">
                    <?= $notificationQueue->notification_content ?>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->Paginator->numbers() ?>
