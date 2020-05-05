<?php

namespace App\Listeners;

use App\Events\SendMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Mail;
use App\UsersModel;

class SendMailFired
{

    public $sender;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->sender = "ovsepanrik@gmail.com";
    }

    /**
     * Handle the event.
     *
     * @param  SendMail  $event
     * @return void
     */
    public function handle(SendMail $event)
    {
        $user  = $event->user->toArray();
        $event = $event->action;
        $appName = "nChat";
        $verifyHash = md5($user["id"].$user["email"]);
        switch ($event) {
            case 'register':
                $template = "Emails.verify";
                $header = "Verify your account";
                $link = 'user/verify/'.$verifyHash.'/'.$user["id"];
                $data = [
                    "user" => $user,
                    "link" => $link
                ];
                $this->Send($template, $data, $user, $header, $appName);
                break;
            case 'forgotPassword':
                $template = "Emails.resetPassword";
                $header = "Reset your account password";
                $link = 'resetPassword/'.$verifyHash.'/'.$user["id"];
                $data = [
                    "user" => $user,
                    "link" => $link
                ];
                $this->Send($template, $data, $user, $header, $appName);
                break;
        }
    }

    public function Send($template, $data, $user, $header, $appName)
    {
        Mail::send($template, $data, function($message) use ($user, $header, $appName) {
            $message->from($this->sender, $appName);
            $message->to($user['email'], $user["name"]);
            $message->subject($header);
        });
    }
}
