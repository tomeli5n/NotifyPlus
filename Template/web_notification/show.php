<?php use Kanboard\Plugin\NotifyPlus\Helper\DateHelper; ?>

<div class="page-header">
        <h2><?= t('My notifications') ?></h2>
    </div>
    <div class="notifications-modal">
    <div>
    <?php if (empty($notifications)): ?>
        <p class="alert"><?= e('No notification.') ?></p>
    <?php else: ?>
        <div class="notification-count"> 
            <?= t('New Activity') . $nb_notifications ?> <?= $nb_notifications > 1 ? t('notifications') : t('notification') ?>
        </div>

        <div class="notification-list">
            <?php foreach ($groupedNotifications as $group): ?>
                <div class="notification-item">
                    <div class="notification-content">
                        <div class="notification-project">
                            <span>
                            <?= $this->text->e($group['project_name'] . " > " . ($group['column_title'] ?? '').(isset($group['task_id']) ? " #".$group['task_id'] : '')) ?>
                            </span>
                        </div>
                        <h3 class="notification-title <?= isset($group['is_active']) && !$group['is_active'] ? 'closed' : '' ?>">
                                <?= 
                                
                                    $this->url->link(
                                    isset($group['task_id']) ? $group['title'] : $group['title'],
                                    'ReadNotificationController',
                                    'redirect',
                                    array(
                                        'plugin' => 'NotifyPlus',
                                        'user_id' => $user['id'],
                                        'notification_id' => $group['notification_id'],
                                        'task_id' => $group['task_id'] ?? null,
                                        'project_id' => $group['project_id'],
                                        'csrf_token' => $this->app->getToken()->getReusableCSRFToken()
                                    )
                                ) ?>
                        </h3>
                    </div>
                    <div class="notification-right">
                        <span class="notification-date">
                            <?= DateHelper::time_elapsed_string($group['date_creation']) ?>
                        </span>
                        <span class="notification-mark-read">
                            <?= $this->modal->replaceIconLink('check', '', 'ReadNotificationController', 'discard', array(
                                'plugin' => 'NotifyPlus',
                                'user_id' => $user['id'], 
                                'task_id' => $group['task_id'] ?? 0,
                                'project_id' => $group['project_id'],
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