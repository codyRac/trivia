<?php

use App\Models\Credit;
use App\Models\Service;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config(['services.slack.webhook_url' => 'https://hooks.slack.test/abc']);
    Http::fake(['hooks.slack.test/*' => Http::response('ok')]);
});

function service(array $attributes = []): Service
{
    return Service::create(['category' => 'Chores', 'title' => 'Back massage', 'cost' => 10, ...$attributes]);
}

test('redeeming a service notifies slack', function () {
    Credit::create(['credits' => 50, 'earned' => 50, 'spent' => 0]);
    $service = service();

    $this->postJson('/use-credits', ['service_id' => $service->id])->assertOk();

    Http::assertSent(fn ($request) => str_contains($request['text'], '*Back massage* was just redeemed for 10 credits')
        && str_contains($request['text'], '1 of these still to do · 40 credits left'));
});

test('redeeming still works when slack is down or not configured', function () {
    Credit::create(['credits' => 50, 'earned' => 50, 'spent' => 0]);
    Http::fake(fn () => throw new Illuminate\Http\Client\ConnectionException('down'));

    $this->postJson('/use-credits', ['service_id' => service()->id])->assertOk();

    config(['services.slack.webhook_url' => null]);
    $this->postJson('/use-credits', ['service_id' => service(['title' => 'Dishes'])->id])->assertOk();
});

test('friday review lists only what has not been done yet', function () {
    service(['title' => 'Back massage', 'times_used' => 3, 'fulfilled' => 1]);
    service(['title' => 'Dishes', 'times_used' => 2, 'fulfilled' => 2]);
    service(['title' => 'Movie pick', 'times_used' => 1, 'fulfilled' => 0]);

    $this->artisan('slack:unfulfilled-review')->assertSuccessful();

    Http::assertSent(fn ($request) => str_contains($request['text'], '3 still to do')
        && str_contains($request['text'], '*Back massage* — 2 owed (redeemed 3, done 1)')
        && str_contains($request['text'], '*Movie pick* — 1 owed')
        && ! str_contains($request['text'], 'Dishes'));
});

test('friday review says so when everything is done', function () {
    $this->artisan('slack:unfulfilled-review')->assertSuccessful();

    Http::assertSent(fn ($request) => str_contains($request['text'], 'nothing outstanding'));
});
