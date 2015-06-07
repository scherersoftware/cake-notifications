<?php
$this->assign('title', __d('notifications', 'notification contents.headline') . ' - ' . __d('notifications', 'notification_contents.view'));
?>
<h2 class="page-header">
    <?= __d('notifications', 'notification_contents.view') ?>
    <div class="pull-right">
        <?= $this->Html->link('<i class="fa fa-pencil fa-fw"></i><span class="button-text">' . __d('notifications', 'notification_contents.edit') .'</span>', ['action' => 'edit', $notificationContent->id], ['class' => 'btn btn-xs btn-warning edit-button', 'escape' => false]) ?>
        <?= $this->Html->link('<i class="fa fa-arrow-left fa-fw"></i><span class="button-text">' . __d('notifications', 'forms.back_to_list') .'</span>', ['action' => 'index'], ['class' => 'btn btn-xs btn-primary back-button', 'escape' => false]) ?>
    </div>
</h2>
<dl class="dl-horizontal">
	<dt><?= __d('notifications', 'notification_content.notification_identifier') ?></dt>
	<dd><?= h($notificationContent->notification_identifier) ?></dd>

	<dt><?= __d('notifications', 'notification_content.notes') ?></dt>
	<dd><?= $this->Text->autoParagraph($notificationContent->notes) ?></dd>

	<dt><?= __d('notifications', 'notification_content.created') ?></dt>
	<dd><?= h($notificationContent->created) ?></dd>

	<dt><?= __d('notifications', 'notification_content.modified') ?></dt>
	<dd><?= h($notificationContent->modified) ?></dd>

</dl>
