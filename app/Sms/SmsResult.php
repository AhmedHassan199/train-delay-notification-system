<?php

namespace App\Sms;

class SmsResult
{
    public function __construct(
        public string $to,
        public string $body,
        public ?string $provider = null,
        public int $attempts = 1,
        public ?string $reference = null,
    ) {
    }

    public function withProvider(string $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function withAttempts(int $attempts): self
    {
        $this->attempts = $attempts;

        return $this;
    }
}
