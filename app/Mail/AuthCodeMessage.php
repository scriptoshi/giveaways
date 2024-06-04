<?php
namespace App\Mail;
use Illuminate\Notifications\Messages\MailMessage;

class AuthCodeMessage extends MailMessage
{
    public $markdown = 'emails.auth.code';
    /**
     * The "level" of the notification (info, success, error).
     *
     * @var string
     */
    public $code = null;

    public function code($code)
    {
        $this->code = $code;
        return $this;
    }
    /**
     * Get an array representation of the message.
     *
     * @return array
     */
    public function toArray()
    {

        return array_merge(parent::toArray(), [
            'codeText' => $this->code
        ]);
    }
}
