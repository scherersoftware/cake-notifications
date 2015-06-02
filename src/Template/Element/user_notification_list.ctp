<?php if(!$excludeMessageListWrapper) : ?>
    <div class="messages-list" id="main-navbar-messages">
<?php endif; ?>

    <?php if (empty($unreadNotifications)) : ?>
        no message here
    <?php else : ?>
        <?php foreach($unreadNotifications as $key => $notification) : ?>
            <?php echo $this->UserNotification->renderUserNotification($notification) ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if ($moreEntriesAvailable) : ?>
        <a href="javascript:" data-page="<?php echo ++$page ?>" class="messages-link">MORE MESSAGES</a>
    <?php endif; ?>

<?php if(!$excludeMessageListWrapper) : ?>
    </div>
<?php endif; ?>
