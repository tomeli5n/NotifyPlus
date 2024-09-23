<?php

namespace Kanboard\Plugin\NotifyPlus\Controller;

use Kanboard\Controller\BaseController;

class ReadNotificationController extends \Kanboard\Controller\BaseController
{
     /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'user_has_unread_notifications';

    public function show()
    {
        $user = $this->getUser();
        $notifications = $this->userUnreadNotificationModel->getAll($user['id']);

        $groupedNotifications = [];
        foreach ($notifications as $notification) {
            if ($notification['event_name'] === 'task.overdue') {
                $this->handleOverdueNotification($notification, $groupedNotifications);
            } else {
                $this->handleRegularNotification($notification, $groupedNotifications);
            }
        }

        $this->response->html($this->template->render('web_notification/show', array(
            'notifications'    => $notifications,
            'groupedNotifications'  => $groupedNotifications,
            'nb_notifications' => count($groupedNotifications),
            'user'             => $user,
        )));
    }

    private function handleOverdueNotification($notification, &$groupedNotifications)
    {
        $projectId = $notification['event_data']['tasks'][0]['project_id'];
        $projectName = $notification['event_data']['project_name'];
        $key = "overdue_{$projectId}";

        if (!isset($groupedNotifications[$key])) {
            $groupedNotifications[$key] = [
                'project_id' => $projectId,
                'project_name' => $projectName,
                'event_name' => 'task.overdue',
                'date_creation' => $notification['date_creation'],
                'notification_id' => $notification['id'],
                'tasks' => [],
                'count' => 0,
            ];
        }

        foreach ($notification['event_data']['tasks'] as $task) {
            $taskId = $task['id'];
            if (!isset($groupedNotifications[$key]['tasks'][$taskId])) {
                $groupedNotifications[$key]['tasks'][$taskId] = $task;
                $groupedNotifications[$key]['count']++;
            }
        }

        $groupedNotifications[$key]['title'] = $this->generateOverdueTitle($groupedNotifications[$key]['count'], $projectName);
    }

    private function handleRegularNotification($notification, &$groupedNotifications)
    {
        $taskId = $notification['event_data']['task']['id'];
        $key = "task_{$taskId}";

        if (!isset($groupedNotifications[$key])) {
            $groupedNotifications[$key] = [
                'task_id' => $taskId,
                'project_name' => $notification['event_data']['task']['project_name'],
                'project_id' => $notification['event_data']['task']['project_id'],
                'title' => $notification['event_data']['task']['title'],
                'is_active' => $notification['event_data']['task']['is_active'],
                'column_title' => $notification['event_data']['task']['column_title'],
                'date_creation' => $notification['date_creation'],
                'notification_id' => $notification['id'],
                'notifications' => [],
            ];
        }

        $groupedNotifications[$key]['notifications'][] = $notification;
    }

    private function generateOverdueTitle($count, $projectName)
    {
        return $count > 1 ? "{$projectName} : " .e('%d overdue tasks', $count) : "{$count} tareas atrasadas en {$projectName}";
    }

    public function redirect()
    {
        $user_id = $this->getUserId();
        $notification_id = $this->request->getIntegerParam('notification_id');
        $task_id = $this->request->getIntegerParam('task_id');
        $project_id = $this->request->getIntegerParam('project_id');
        $notification = $this->userUnreadNotificationModel->getById($notification_id);
        

        $this->ReadNotification($user_id, $task_id, $project_id);

        if (empty($notification)) {
            $this->show();
        } elseif ( $notification['task_id'] == 0 ) {
            $this->response->redirect($this->helper->url->to(
                'ProjectViewController',
                'show',
                array('project_id' => $project_id)));
        } elseif ($this->helper->text->contains($notification['event_name'], 'comment')) {
            $this->response->redirect($this->helper->url->to(
                'TaskViewController',
                'show',
                array('task_id' => $this->notificationModel->getTaskIdFromEvent($notification['event_name'], $notification['event_data'])),
                'comment-'.$notification['event_data']['comment']['id']
            ));
        } else {
            $this->response->redirect($this->helper->url->to(
                'TaskViewController',
                'show',
                array('task_id' => $this->notificationModel->getTaskIdFromEvent($notification['event_name'], $notification['event_data']))
            ));
        }
    }
    
    public function discard()
    {
        $user_id = $this->getUserId();
        $notification_id = $this->request->getIntegerParam('notification_id');
        $task_id = $this->request->getIntegerParam('task_id');
        $project_id = $this->request->getIntegerParam('project_id');

        $notification = $this->userUnreadNotificationModel->getById($notification_id);
        
        $this->ReadNotification($user_id, $task_id, $project_id);

        $this->show();

    }
    private function getUserId()
    {
        $user_id = $this->request->getIntegerParam('user_id');

        if (! $this->userSession->isAdmin() && $user_id != $this->userSession->getId()) {
            $user_id = $this->userSession->getId();
        }

        return $user_id;
    }
    private function ReadNotification($user_id, $task_id, $project_id)
    {
        if( $task_id > 0 ){
            return $this->db->table(self::TABLE)->like('event_data', '%"task_id":' . $task_id . ',%')->eq('user_id', $user_id)->remove();
        } else {
            return $this->db->table(self::TABLE)->like('event_data', '%"project_id":' . $project_id . ',%')
            ->eq('user_id', $user_id)
            ->eq('event_name', 'task.overdue')
            ->remove();
        }   
    }
}

?>