<?php

namespace App\Sms\Drivers;

use App\Sms\SmsDriverInterface;
use App\Sms\SmsResult;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class TwilioSmsDriver implements SmsDriverInterface
{
    public function __construct(
        private ?string $sid,
        private ?string $token,
        private ?string $from,
    ) {
    }

    public function name(): string
    {
        return 'twilio';
    }

    public function send(string $to, string $body): SmsResult
    {
        if (! $this->sid || ! $this->token || ! $this->from) {
            throw new RuntimeException('Twilio is not configured (missing SID/token/from).');
        }

        $response = Http::asForm()
            ->withBasicAuth($this->sid, $this->token)
            ->timeout(10)
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->sid}/Messages.json", [
                'To' => $to,
                'From' => $this->from,
                'Body' => $body,
            ]);

        $response->throw();

        return new SmsResult(
            to: $to,
            body: $body,
            provider: $this->name(),
            reference: $response->json('sid'),
        );
    }
}
