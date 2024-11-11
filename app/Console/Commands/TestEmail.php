<?php

namespace App\Console\Commands;

use App\Mail\CheckInOutNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    protected $signature = 'test:email';
    protected $description = 'Gửi thử email nhắc nhở giờ check-in/check-out';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $type = 'check_in'; // Hoặc 'check_out' tùy vào nhu cầu
        $toEmail = 'kurehakisaya@gmail.com'; // Đảm bảo email hợp lệ

        try {
            // Truyền trực tiếp giá trị 'check_in' vào constructor
            Mail::to($toEmail)->send(new CheckInOutNotification($type));
            $this->info('Email đã được gửi thành công!');
        } catch (\Exception $e) {
            $this->error('Lỗi khi gửi email: ' . $e->getMessage());
        }
    }
}
