<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckInOutNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $type;

    public function __construct($type = 'default')  // Đảm bảo có giá trị mặc định là 'default'
    {
        $this->type = $type;

        // Log giá trị type khi nhận vào
        Log::info('Type passed to email: ' . $this->type);
    }

    public function build()
    {
        // Đặt subject dựa trên $type
        $subject = $this->type === 'check_in' ? 'Nhắc nhở giờ check-in' : 'Nhắc nhở giờ check-out';

        // Xây dựng email
        return $this->view('emails.checkinout')
            ->subject($subject)
            ->with(['type' => $this->type]);
    }
}
