<h2 class="page-header">
	<?= __d('notifications', 'notification contents.headline') ?>
</h2>
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th><?= $this->Paginator->sort('notification_identifier', __d('notifications', 'notification_content.notification_identifier')) ?></th>
			<th><?= $this->Paginator->sort('created', __('created')) ?></th>
			<th><?= $this->Paginator->sort('modified', __('modified')) ?></th>
			<th class="actions"><?= __('lists.actions') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($notificationContents as $notificationContent): ?>
		<tr>
			<td><?= h($notificationContent->notification_identifier) ?></td>
			<td><?= h($notificationContent->created) ?></td>
			<td><?= h($notificationContent->modified) ?></td>
			<td class="actions">
				<?= $this->Html->link(__('lists.view'), ['action' => 'view', $notificationContent->id]) ?>
				<?= $this->Html->link(__('lists.edit'), ['action' => 'edit', $notificationContent->id]) ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?= $this->Paginator->numbers() ?>

<div class="actions">
	<h3><?= __('lists.actions') ?></h3>
	<ul>
		<li><?= $this->Html->link(__d('notifications', 'notification_contents.add'), ['action' => 'add']) ?></li>
	</ul>
</div>
