<?php
use Cake\Core\Configure;
    $this->set('loadCkEditor', true);
    $this->assign('title', __d('notifications', 'notification contents.headline') . ' - ' . (($this->request->action === 'add') ? __d('notifications', 'notification_contents.add') : __d('notifications', 'notification_contents.edit')));
?>
<h2 class="page-header">
    <?= __d('notifications', 'notification contents.headline'); ?> <?= ($this->request->action === 'add') ? 'erstellen' : 'bearbeiten' ?>
    <div class="pull-right">
        <?= $this->Html->link('<i class="fa fa-arrow-left fa-fw"></i><span class="button-text">' . __d('notifications', 'forms.back_to_list') .'</span>', ['action' => 'index'], ['class' => 'btn btn-xs btn-primary back-button', 'escape' => false]) ?>
    </div>
</h2>


<div>
    <ul class="nav nav-tabs" role="tablist">
        <?php foreach ($translations as $language => $notificationContent): ?>
            <?php
                $active = '';
                if ($this->request->data['locale'] == $language) {
                    $active = 'active';
                }
            ?>
            <li role="presentation" class="<?=$active?>"><a href="#<?= $language ?>" aria-controls="<?= $language ?>" role="tab" data-toggle="tab"><?= $language ?></a></li>
        <?php endforeach; ?>
    </ul>

    <div class="tab-content">
        <?php foreach ($translations as $language => $notificationContent): ?>
            <?php
                $active = '';
                if ($this->request->data['locale'] == $language) {
                    $active = 'active';
                }
            ?>
            <div role="tabpanel" class="tab-pane <?= $active ?>" id="<?= $language ?>">
                <?= $this->Form->create($notificationContent, ['align' => 'horizontal']) ?>
                    <fieldset>
                        <?php
                            echo $this->Form->input('locale', [
                                'type' => 'hidden',
                                'value' => $language
                            ]);
                            echo $this->Form->input('notification_identifier', [
                                'label' => __d('notifications', 'notification_content.notification_identifier')
                            ]);
                            echo $this->Form->input('notes', [
                                'label' => __d('notifications', 'notification_content.notes')
                            ]);
                        ?>
                    </fieldset>

                    <?php if (isset($transports['email'])): ?>

                        <fieldset>
                            <legend>E-Mail Transport</legend>

                            <?php echo $this->Form->input('email_subject', [
                                'label' => __d('notifications', 'notification_content.email_subject')
                            ]) ?>
                            <?php echo $this->Form->input('email_html', [
                                'type' => 'textarea',
                                'label' => __d('notifications', 'notification_content.email_body'),
                                'class' => 'wysiwyg'
                            ]) ?>

                        </fieldset>

                    <?php endif; ?>

                    <?php if (isset($transports['push_message'])): ?>

                        <fieldset>
                            <legend>Push Message Transport</legend>

                            <?php echo $this->Form->input('push_message', [
                                'label' => __d('notifications', 'notification_content.push_message')
                            ]) ?>

                        </fieldset>

                    <?php endif; ?>

                    <?php if (isset($transports['hipchat'])): ?>

                        <fieldset>

                            <legend>HipChat Transport</legend>

                            <?php echo $this->Form->input('hipchat_message', [
                                'label' => __d('notifications', 'notification_content.hipchat_message')
                            ]) ?>

                        </fieldset>

                    <?php endif; ?>

                    <?php if (isset($transports['sms'])): ?>

                        <fieldset>
                            <legend>SMS Message Transport</legend>

                            <?php echo $this->Form->input('sms', [
                                'label' => __d('notifications', 'notification_content.sms')
                            ]) ?>

                        </fieldset>

                    <?php endif; ?>

                    <?php if (isset($transports['onpage'])): ?>

                        <fieldset>
                            <legend>OnPage Message Transport</legend>

                            <?php echo $this->Form->input('onpage', [
                                'type' => 'textarea',
                                'label' => __d('notifications', 'notification_content.onpage_message'),
                            ]) ?>

                            <?php echo $this->Form->input('onpage_link', [
                                'label' => __d('notifications', 'notification_content.onpage_link') . '*',
                            ]) ?>

                            <?php echo $this->Form->input('onpage_link_title', [
                                'label' => __d('notifications', 'notification_content.onpage_link_title') . '*',
                                'maxlength' => 25
                            ]) ?>

                        </fieldset>
                *<?= __d('notifications', 'notification_content.onpage_link_may_have_no_effect') ?>

                    <?php endif; ?>
                <br><br>
                <?= $this->Form->button(__d('notifications', 'forms.save'), ['class' => 'btn-success']) ?>
                <br><br>
                <?= $this->Form->end() ?>
            </div>
        <?php endforeach ?>
    </div>  
</div>