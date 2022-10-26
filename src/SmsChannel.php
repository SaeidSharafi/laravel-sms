<?php

namespace SaeidSharafi\LaravelSms;

use Illuminate\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use SaeidSharafi\LaravelSms\Exceptions\CouldNotUseNotification;

class SmsChannel
{
    protected $events;

    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     *
     * @throws Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toSms')) {
            if ($sms = $notification->toSms($notifiable)) {
                try {
                    if (config(('app.debug'))) {
                        \Log::info(print_r($sms, true));
                    }
                    $result = $sms->send();
                    \Log::info("SMS result: ".$result);
                    return $result;
                } catch (\Exception $e) {
                    $this->events->dispatch(new NotificationFailed($notifiable,
                        $notification, get_class($this), ['exception' => $e]));
                }
            }
        } else {
            throw CouldNotUseNotification::missingMethod();
        }

        return false;
    }
}
