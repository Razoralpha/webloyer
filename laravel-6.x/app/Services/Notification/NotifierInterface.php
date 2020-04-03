<?php

namespace App\Services\Notification;

interface NotifierInterface
{
    /**
     * Recipient of notification.
     *
     * @var string
     */
    public function to($to);

    /**
     * Sender of notification.
     *
     * @param string $from The sender
     * @return App\Services\Notification\NotifierInterface Return self for chainability
     */
    public function from($from);

    /**
     * Send notification.
     *
     * @param string $subject The subject of notification
     * @param string $message The message of notification
     * @return void
     */
    public function notify($subject, $message);
}
