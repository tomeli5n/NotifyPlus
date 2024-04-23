<span class="notification">
<?php if ($this->user->hasNotifications()): ?>
    <?= $this->modal->mediumIcon('bell web-notification-icon', t('Unread notifications'), 'ReadNotificationController', 'show', array('plugin' => 'NotifyPlus',
        'user_id' => $this->user->getId())) ?>
<?php else: ?>WebNotificationController
    <?= $this->modal->mediumIcon('bell', t('My notifications'), 'ReadNotificationController', 'show', array(
        'plugin' => 'NotifyPlus',
        'user_id' => $this->user->getId())) ?>
<?php endif ?>
</span>
