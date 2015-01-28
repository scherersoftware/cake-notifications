<h2><?= __d('notifications', 'notification_contents.view') ?></h2>

<dl class="dl-horizontal">
	<dt><?= __d('notifications', 'notification_content.id') ?></dt>
	<dd><?= h($notificationContent->id) ?></dd>

	<dt><?= __d('notifications', 'notification_content.notification_identifier') ?></dt>
	<dd><?= h($notificationContent->notification_identifier) ?></dd>

	<dt><?= __d('notifications', 'notification_content.notes') ?></dt>
	<dd><?= h($notificationContent->notes) ?></dd>

	<dt><?= __d('notifications', 'notification_content.created') ?></dt>
	<dd><?= h($notificationContent->created) ?></dd>

	<dt><?= __d('notifications', 'notification_content.modified') ?></dt>
	<dd><?= h($notificationContent->modified) ?></dd>

</dl>

<div class="actions">
	<h3><?= __('lists.actions'); ?></h3>
	<ul>
		<li><?= $this->Html->link(__d('notifications', 'notification_contents.edit'), ['action' => 'edit', $notificationContent->id]) ?> </li>
		<li><?= $this->Html->link(__('lists.back_to_list'), ['action' => 'index']) ?> </li>
	</ul>
</div>
