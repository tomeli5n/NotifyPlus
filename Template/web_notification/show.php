<div class="page-header">
        <h2><?= t('My notifications') ?></h2>
    </div>
    <div class="notifications-modal">
    <div>
    <?php if (empty($notifications)): ?>
        <p class="alert"><?= t('No notification.') ?></p>
    <?php else: ?>
        <div class="notification-count">
            <?= $nb_notifications ?> <?= $nb_notifications > 1 ? t('notifications') : t('notification') ?>
        </div>

        <div class="notification-list">
            <?php foreach ($groupedNotifications as $group): ?>
                <div class="notification-item">
                    <div class="notification-content">
                        <div class="notification-project">
                            <?= $this->url->link(
                                $this->text->e($group['project_name'] . " > " . ($group['column_title'] ?? 'Unknown Column')),
                                'BoardViewController',
                                'show',
                                array('project_id' => $group['project_id'])
                            ) ?>
                        </div>
                        <h3 class="notification-title <?= isset($group['is_active']) && !$group['is_active'] ? 'closed' : '' ?>">
                            <?= $this->url->link("#".$group['task_id']." ".$group['title'], 'ReadNotificationController', 'redirect', array(
                                'plugin' => 'NotifyPlus',
                                'user_id' => $user['id'],
                                'notification_id' => $group['notification_id'],
                                'task_id' => $group['task_id'],
                                'csrf_token' => $this->app->getToken()->getReusableCSRFToken()
                            )) ?>
                        </h3>
                    </div>
                    <div class="notification-right">
                        <span class="notification-date">
                            <?= date('d/M H:i', $group['date_creation']) ?>
                        </span>
                        <span class="notification-mark-read">
                            <?= $this->modal->replaceIconLink('check', '', 'ReadNotificationController', 'discard', array(
                                'plugin' => 'NotifyPlus',
                                'user_id' => $user['id'], 
                                'task_id' => $group['task_id'],
                                'csrf_token' => $this->app->getToken()->getReusableCSRFToken()
                            )) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
    <div class="notification-actions">
        <?= $this->modal->replaceIconLink('check-square-o', t('Mark all as read'), 'WebNotificationController', 'flush', array(
            'user_id' => $user['id'], 
            'csrf_token' => $this->app->getToken()->getReusableCSRFToken()
        )) ?>
    </div>
    <?php endif ?>
</div>