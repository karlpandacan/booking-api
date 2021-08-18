<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Mailer extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The data instance.
     *
     * @var Data
     */
    protected $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->view($this->data->template)
            ->subject($this->data->subject)
            ->with(['data' => $this->data->data])
            ->with(['logo' => $this->data->logo]);

        $mail = $mail->from($this->data->mailFromAddress, $this->data->mailFromName);

        if (isset($this->data->attachment)) {
            if ($this->data->isAttachFromStorage) {
                if (isset($this->data->attachmentName)) {
                    $mail = $mail->attachFromStorageDisk(
                        $this->data->storageDisk,
                        $this->data->attachment,
                        null,
                        ['as' => $this->data->attachmentName]
                    );
                } else {
                    $mail = $mail->attachFromStorageDisk(
                        $this->data->storageDisk,
                        $this->data->attachment
                    );
                }
            } else {
                if (isset($this->data->attachmentName)) {
                    $mail = $mail->attach($this->data->attachment, ['as' => $this->data->attachmentName]);
                } else {
                    $mail = $mail->attach($this->data->attachment);
                }
            }
        }

        return $mail;
    }
}
