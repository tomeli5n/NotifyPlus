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

        $this->response->html($this->template->render('web_notification/show', array(
            'notifications'    => $notifications,
            'nb_notifications' => count($notifications),
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