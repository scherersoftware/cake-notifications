<?php 
    $this->set('loadCkEditor', true);
    $this->assign('title', __d('notifications', 'notification contents.headline') . ' - ' . (($this->request->action === 'add') ? __d('notifications', 'notification_contents.add') : __d('notifications', 'notification_contents.edit')));
?>

<?= $this->Form->create($notificationContent, ['horizontal' => true]) ?>
	<fieldset>
		<legend><?= __d('notifications', 'notification_contents.form'); ?></legend>
		<?php
			echo $this->Form->input('notification_identifier', [
				'label' => __d('notifications', 'notification_content.notification_identifier')
			]);
			echo $this->Form->input('notes', [
				'label' => __d('notifications', 'notification_content.notes')
			]);
		?>
	</fieldset>
	<fieldset>
		<legend>E-Mail Transport</legend>
		
		<?php echo $this->Form->input('email_subject') ?>
		<?php echo $this->Form->input('email_html', [
			'type' => 'textarea',
			'label' => 'Email Body',
			'class' => 'wysiwyg'
		]) ?>
		
	</fieldset>
	<fieldset>
		<legend>Push Message Transport</legend>
		
		<?php echo $this->Form->input('push_message') ?>
		
	</fieldset>
	
	<fieldset>
		<legend>SMS Message Transport</legend>
		
		<?php echo $this->Form->input('sms_message') ?>
		
	</fieldset>
	
<?= $this->Form->submit() ?>


<div class="actions">
	<h3><?= __('forms.actions') ?></h3>
	<ul>
		<li><?= $this->Html->link(__('lists.back_to_list'), ['action' => 'index']) ?></li>
	</ul>
</div>