<?php

namespace App\Sms;

interface SmsDriverInterface
{

    public function send(string $to, string $body): SmsResult;

    public function name(): string;
}
