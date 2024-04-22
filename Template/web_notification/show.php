<div class="page-header">
    <h2><?= t('Mis Notificaciones') ?></h2>
</div>
<?php if (empty($notifications)): ?>
    <p class="alert"><?= t('No notification.') ?></p>
<?php else: ?>
    <?php 
        // Todo: move to controller
        $groupedNotifications = [];
        foreach ($notifications as $notification) {
            $task_id = $notification['event_data']['task']['id']; // Asumimos que siempre hay un task_id
            //if (!isset($groupedNotifications[$task_id])) {
                $groupedNotifications[$task_id] = [
                    'task_id' => $task_id,
                    'project_name' => $notification['event_data']['task']['project_name'],
                    'project_id' => $notification['event_data']['task']['project_id'],
                    'title' => $notification['event_data']['task']['title'],
                    'date_creation' => $notification['date_creation'], // Usar la fecha más reciente o una lógica específica
                    'notification_id' => $notification['id'], // para retrocompatibilidad con metodos de controller
                    'notifications' => []
                ];
            //}
            $groupedNotifications[$task_id]['notifications'][] = $notification;
        } ?>
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
    <div class="table-list-row table-border-left">
        <h2>
        <span class="table-list-title">
            <?= $this->url->link($group['title'], 'ReadNotificationController', 'redirect', array(
                'plugin' => 'NotifyPlus',
                'user_id' => $user['id'],
                'notification_id' => $notification['id'],
                'task_id' => $group['task_id'],
                'csrf_token' => $this->app->getToken()->getReusableCSRFToken())) ?>
        </span>
        </h2>
    </div>
    <div class="table-list-details">

    <?= $this->url->link(
            $this->text->e($group['project_name']),
            'BoardViewController',
            'show',
            array('project_id' => $group['project_id'])
        ) ?> &gt;
    <i class="fa fa-tasks fa-fw"></i>
        <?= $this->dt->datetime($group['date_creation']) ?>
        <?= $this->modal->replaceIconLink('check', t('Marcar como leida'), 'ReadNotificationController', 'discard', array(
                'plugin' => 'NotifyPlus',
                'user_id' => $user['id'], 
                'task_id' => $group['task_id'],
                'csrf_token' => $this->app->getToken()->getReusableCSRFToken())) ?>
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