<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Recurrency extends Mailable
{
    use Queueable, SerializesModels;

    protected $name;
    protected $value;
    protected $created_at;
    protected $thanks;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content)
    {
        $this->name = $content['name'];
        $this->value = $content['value'];
        $this->pix = $content['pix'];
        $this->thanks = $content['thanks'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mails.recurrency', [
            'name' => $this->name,
            'value' => $this->value,
            'pix' => $this->pix,
            'thanks' => $this->thanks,
        ]);
    }
}
