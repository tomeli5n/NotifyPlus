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
            $task_id = $notification['event_data']['task']['id']; // Asumimos que siempre hay un task_id
            //if (!isset($groupedNotifications[$task_id])) {
                $groupedNotifications[$task_id] = [
                    'task_id' => $task_id,
                    'project_name' => $notification['event_data']['task']['project_name'],
                    'project_id' => $notification['event_data']['task']['project_id'],
                    'title' => $notification['event_data']['task']['title'],
                    'column_title' =>  $notification['event_data']['task']['column_title'],
                    'date_creation' => $notification['date_creation'], // Usar la fecha más reciente o una lógica específica
                    'notification_id' => $notification['id'], // para retrocompatibilidad con metodos de controller
                    'notifications' => []
                ];
            //}
            $groupedNotifications[$task_id]['notifications'][] = $notification;
        }

        $this->response->html($this->template->render('web_notification/show', array(
            'notifications'    => $notifications,
            'groupedNotifications'  => $groupedNotifications,
            'nb_notifications' => count($groupedNotifications),
            'user'             => $user,
        )));
    }
    
    public function redirect()
    {
        $user_id = $this->getUserId();
        $notification_id = $this->request->getIntegerParam('notification_id');
        $task_id = $this->request->getIntegerParam('task_id');

        $notification = $this->userUnreadNotificationModel->getById($notification_id);
        

        $this->ReadNotification($user_id, $task_id);

        if (empty($notification)) {
            $this->show();
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

        $notification = $this->userUnreadNotificationModel->getById($notification_id);
        
        $this->ReadNotification($user_id, $task_id);

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
    private function ReadNotification($user_id, $task_id)
    {
        return $this->db->table(self::TABLE)->like('event_data', '%"task_id":' . $task_id . ',%')->eq('user_id', $user_id)->remove();
    }
}

?>