<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Slack
{
    /**
     * Post a message to the Slack incoming webhook in SLACK_WEBHOOK_URL.
     * Never throws: a Slack outage must not break the site.
     */
    public static function send(string $text): bool
    {
        $url = config('services.slack.webhook_url');

        if (! $url) {
            return false;
        }

        try {
            return Http::timeout(5)->post($url, ['text' => $text])->successful();
        } catch (\Throwable $e) {
            Log::warning('Slack notification failed: ' . $e->getMessage());

            return false;
        }
    }
}
