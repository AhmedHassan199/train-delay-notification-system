<?php

namespace App\Sms\Drivers;

use App\Sms\SmsDriverInterface;
use App\Sms\SmsResult;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class VonageSmsDriver implements SmsDriverInterface
{
    public function __construct(
        private ?string $key,
        private ?string $secret,
        private string $from,
    ) {
    }

    public function name(): string
    {
        return 'vonage';
    }

    public function send(string $to, string $body): SmsResult
    {
        if (! $this->key || ! $this->secret) {
            throw new RuntimeException('Vonage is not configured (missing key/secret).');
        }

        $response = Http::asForm()->timeout(10)->post('https://rest.nexmo.com/sms/json', [
            'api_key' => $this->key,
            'api_secret' => $this->secret,
            'to' => ltrim($to, '+'),
            'from' => $this->from,
            'text' => $body,
        ]);

        $response->throw();

        $message = $response->json('messages.0');
        if (! $message || ($message['status'] ?? '1') !== '0') {
            throw new RuntimeException(
                'Vonage rejected the message: '.($message['error-text'] ?? 'unknown error')
            );
        }

        return new SmsResult(
            to: $to,
            body: $body,
            provider: $this->name(),
            reference: $message['message-id'] ?? null,
        );
    }
}
