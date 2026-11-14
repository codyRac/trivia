<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Support\Slack;
use Illuminate\Console\Command;

class SlackUnfulfilledReview extends Command
{
    protected $signature = 'slack:unfulfilled-review';

    protected $description = 'Send Slack a review of redeemed services that have not been fulfilled yet';

    public function handle(): int
    {
        if (! config('services.slack.webhook_url')) {
            $this->error('SLACK_WEBHOOK_URL is not set.');

            return self::FAILURE;
        }

        $sent = Slack::send(self::message());
        $sent ? $this->info('Review sent to Slack.') : $this->error('Slack did not accept the message.');

        return $sent ? self::SUCCESS : self::FAILURE;
    }

    public static function message(): string
    {
        $services = Service::whereColumn('times_used', '>', 'fulfilled')
            ->orderByRaw('times_used - fulfilled desc')
            ->orderBy('title')
            ->get();

        if ($services->isEmpty()) {
            return ':white_check_mark: *Weekly redeem review* — nothing outstanding. You are all caught up!';
        }

        $lines = $services->map(function (Service $service) {
            $owed = $service->times_used - $service->fulfilled;

            return "• *{$service->title}* — {$owed} owed (redeemed {$service->times_used}, done {$service->fulfilled})";
        });

        $total = $services->sum(fn (Service $service) => $service->times_used - $service->fulfilled);

        return ":clipboard: *Weekly redeem review* — {$total} still to do\n" . $lines->implode("\n");
    }
}
