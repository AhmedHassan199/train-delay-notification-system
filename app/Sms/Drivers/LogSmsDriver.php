<?php

namespace App\Sms\Drivers;

use App\Sms\SmsDriverInterface;
use App\Sms\SmsResult;
use Illuminate\Support\Facades\Log;

class LogSmsDriver implements SmsDriverInterface
{
    public function name(): string
    {
        return 'log';
    }

    public function send(string $to, string $body): SmsResult
    {
        Log::channel(config('logging.default'))->info('[SMS:log] delivered', [
            'to' => $to,
            'body' => $body,
        ]);

        return new SmsResult(to: $to, body: $body, provider: $this->name());
    }
}
