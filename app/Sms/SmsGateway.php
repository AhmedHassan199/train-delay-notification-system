<?php

namespace App\Sms;

use App\Sms\Drivers\LogSmsDriver;
use App\Sms\Drivers\TwilioSmsDriver;
use App\Sms\Drivers\VonageSmsDriver;
use App\Sms\Exceptions\AllSmsProvidersFailedException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class SmsGateway
{

    private array $resolved = [];

    public function send(string $to, string $body): SmsResult
    {
        $attempts = 0;
        $lastError = null;

        foreach ($this->providerOrder() as $name) {
            if ($this->circuitIsOpen($name)) {
                Log::warning("[SMS] skipping '{$name}' — circuit breaker open");
                continue;
            }

            $driver = $this->driver($name);
            if (! $driver) {
                continue;
            }

            $attempts++;
            try {
                $result = $driver->send($to, $body);
                $this->recordSuccess($name);

                return $result->withProvider($name)->withAttempts($attempts);
            } catch (Throwable $e) {
                $lastError = $e;
                $this->recordFailure($name);
                Log::warning("[SMS] provider '{$name}' failed, failing over", [
                    'to' => $to,
                    'error' => $e->getMessage(),
                ]);

            }
        }

        throw new AllSmsProvidersFailedException($to, $attempts, $lastError);
    }

    private function providerOrder(): array
    {
        $order = config('services.sms.providers', ['log']);

        return empty($order) ? ['log'] : $order;
    }

    private function driver(string $name): ?SmsDriverInterface
    {
        if (isset($this->resolved[$name])) {
            return $this->resolved[$name];
        }

        $driver = match ($name) {
            'log' => new LogSmsDriver(),
            'twilio' => new TwilioSmsDriver(
                config('services.sms.twilio.sid'),
                config('services.sms.twilio.token'),
                config('services.sms.twilio.from'),
            ),
            'vonage' => new VonageSmsDriver(
                config('services.sms.vonage.key'),
                config('services.sms.vonage.secret'),
                config('services.sms.vonage.from', 'Train'),
            ),
            default => null,
        };

        return $this->resolved[$name] = $driver;
    }

    private function circuitIsOpen(string $name): bool
    {
        $threshold = (int) config('services.sms.circuit_breaker.threshold', 5);

        return Cache::get($this->cbKey($name), 0) >= $threshold;
    }

    private function recordFailure(string $name): void
    {
        $cooldown = (int) config('services.sms.circuit_breaker.cooldown', 120);
        $count = (int) Cache::get($this->cbKey($name), 0) + 1;

        Cache::put($this->cbKey($name), $count, $cooldown);
    }

    private function recordSuccess(string $name): void
    {
        Cache::forget($this->cbKey($name));
    }

    private function cbKey(string $name): string
    {
        return "sms:cb:{$name}";
    }
}
