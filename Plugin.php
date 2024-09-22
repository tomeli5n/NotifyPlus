<?php

namespace Kanboard\Plugin\Notifyplus;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Notification\NotificationInterface;

class Plugin extends Base
{
    public function initialize()
    {
        $this->template->setTemplateOverride('header/user_notifications', 'NotifyPlus:header/user_notifications');
        $this->template->setTemplateOverride('web_notification/show', 'NotifyPlus:web_notification/show');
        $this->hook->on('template:layout:css', array('template' => 'plugins/NotifyPlus/Assets/notifyplus.css'));

    }

    public function getCompatibleVersion()
    {
        return '>=1.0.35';
    }

    public function getPluginName()
    {
        return 'NotifyPlus';
    }

    public function getPluginAuthor()
    {
        return 'Mario Tomelin';
    }

    public function getPluginVersion()
    {
        return '1.1.0';
    }

    public function getPluginDescription()
    {
        return 'Display web notifications grouped by task';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/tomeli5n/NotifyPlus';
    }
}

