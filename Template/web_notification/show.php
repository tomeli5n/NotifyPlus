<div class="page-header">
    <h2><?= t('My notifications') ?></h2>
</div>
<?php if (empty($notifications)): ?>
    <p class="alert"><?= t('No notification.') ?></p>
<?php else: ?>
<div class="table-list">
    <div class="table-list-header">
        <div class="table-list-header-count">
            <?php if ($nb_notifications > 1): ?>
                <?= t('%d notifications', $nb_notifications) ?>
            <?php else: ?>
                <?= t('%d notification', $nb_notifications) ?>
            <?php endif ?>
        </div>
        &nbsp;
    </div>
</div>
    <?php foreach ($groupedNotifications as $group): ?>
        <div class="table-list-details">
            <?= $this->dt->datetime($group['date_creation']) ?>
            <i class="fa fa-tasks fa-fw"></i>
            <?= $this->url->link(
                $this->text->e($group['project_name']. " > " . $group['column_title']),
                'BoardViewController',
                'show',
                array('project_id' => $group['project_id'])
            ) ?> &gt;
            <?= $this->modal->replaceIconLink('check', t('Marcar como leida'), 'ReadNotificationController', 'discard', array(
                'plugin' => 'NotifyPlus',
                'user_id' => $user['id'], 
                'task_id' => $group['task_id'],
                'csrf_token' => $this->app->getToken()->getReusableCSRFToken())) ?>
        </div>
    <div class="table-list-row table-border-left">
        <h2>
        <span class="table-list-title">
            <?= $this->url->link("#".$group['task_id']." ".$group['title'], 'ReadNotificationController', 'redirect', array(
                'plugin' => 'NotifyPlus',
                'user_id' => $user['id'],
                'notification_id' => $group['notification_id'],
                'task_id' => $group['task_id'],
                'csrf_token' => $this->app->getToken()->getReusableCSRFToken())) ?>
        </span>
        </h2>
    </div>


    <?php endforeach ?>
    <?php if (! empty($notifications)): ?>
    <ul>
        <li>
            <?= $this->modal->replaceIconLink('check-square-o', t('Mark all as read'), 'WebNotificationController', 'flush', array('user_id' => $user['id'], 'csrf_token' => $this->app->getToken()->getReusableCSRFToken())) ?>
        </li>
    </ul>
    <?php endif ?>
</div>
<?php endif ?>