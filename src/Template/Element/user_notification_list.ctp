<?php if(!$excludeMessageListWrapper) : ?>
    <div class="messages-list" id="main-navbar-messages">
<?php endif; ?>

    <?php if (empty($unreadNotifications)) : ?>
        <div class="no-messages">
            <?= __d('notifications', 'no_unread_onpage_notifications') ?>
        </div>
    <?php else : ?>
        <?php foreach($unreadNotifications as $notification) : ?>
            <?php echo $this->UserNotification->renderUserNotification($notification) ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if ($moreEntriesAvailable) : ?>
        <a href="javascript:" data-page="<?php echo ++$page ?>" class="messages-link">
            <?= __d('notifications', 'load_more_onpage_notifications') ?>
        </a>
    <?php endif; ?>

<?php if(!$excludeMessageListWrapper) : ?>
    </div>
<?php endif; ?>
