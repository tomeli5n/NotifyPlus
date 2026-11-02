<?php

namespace Kanboard\Plugin\NotifyPlus\Model;

use Kanboard\Core\Security\Role;
use Kanboard\Model\TaskModel;
use Kanboard\Model\UserNotificationModel as BaseUserNotificationModel;

class UserNotificationModel extends BaseUserNotificationModel
{
    // Overdue notifications only go to the owner and the project managers of each board
    public function sendUserNotification(array $user, $event_name, array $event_data)
    {
        if ($event_name === TaskModel::EVENT_OVERDUE) {
            $allowed = array();

            $event_data['tasks'] = array_values(array_filter($event_data['tasks'], function ($task) use ($user, &$allowed) {
                $project_id = $task['project_id'];

                if (! isset($allowed[$project_id])) {
                    $project = $this->projectModel->getById($project_id);
                    $allowed[$project_id] = $project['owner_id'] == $user['id'] ||
                        $this->projectUserRoleModel->getUserRole($project_id, $user['id']) === Role::PROJECT_MANAGER;
                }

                return $allowed[$project_id];
            }));

            if (empty($event_data['tasks'])) {
                return;
            }
        }

        parent::sendUserNotification($user, $event_name, $event_data);
    }
}
