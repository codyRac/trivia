<?php

use App\Filament\Admin\Pages\HuntControl;
use App\Models\Hunt;
use App\Models\HuntStop;
use App\Models\User;
use Livewire\Livewire;

test('the hunt is hidden until it is started', function () {
    $this->get('/hunt')->assertRedirect('/holding');
    $this->getJson('/hunt/state')->assertJson(['status' => 'off', 'intro' => null, 'stop' => null]);
    $this->get('/holding')->assertOk();
});

test('starting shows the intro, then next shows only the first clue', function () {
    $hunt = Hunt::current();
    $hunt->start();

    $this->getJson('/hunt/state')->assertJson(['status' => 'intro', 'stop' => null]);

    $hunt->advance();

    $state = $this->getJson('/hunt/state')->json();
    expect($state['status'])->toBe('active')
        ->and($state['stopNumber'])->toBe(1)
        ->and($state['stopCount'])->toBe(7)
        ->and($state['maxTotal'])->toBe(70)
        ->and($state['stop']['clues'])->toHaveCount(1)
        ->and($state['stop']['worth'])->toBe(10)
        ->and($state['stop']['answer'])->toBeNull()
        ->and(json_encode($state))->not->toContain('Broadway Palace');

    $this->get('/hunt')->assertOk();
});

test('revealing clues lowers what the stop is worth', function () {
    $hunt = Hunt::current();
    $hunt->start();
    $hunt->advance();
    $stop = $hunt->currentStop();

    $worth = fn () => $this->postJson('/hunt/reveal', ['stop_id' => $stop->id])->assertOk()->json('hunt.stop');

    expect($worth()['worth'])->toBe(7);
    expect($worth()['worth'])->toBe(4);

    $last = $worth();
    expect($last['worth'])->toBe(1)
        ->and($last['answer'])->toBe('Broadway Palace')
        ->and($last['nextWorth'])->toBeNull();

    // Nothing left to reveal
    expect($worth()['worth'])->toBe(1);
});

test('advancing banks the points and moves to the next stop', function () {
    $hunt = Hunt::current();
    $hunt->start();
    $hunt->advance();
    $first = $hunt->currentStop();

    $this->postJson('/hunt/reveal', ['stop_id' => $first->id]);
    $hunt->advance();

    $state = $this->getJson('/hunt/state')->json();
    expect($state['total'])->toBe(7)
        ->and($state['stopNumber'])->toBe(2)
        ->and($state['stop']['worth'])->toBe(10)
        ->and($state['completed'])->toBe([['number' => 1, 'title' => 'Broadway Palace', 'points' => 7]]);

    // A stale reveal for the old stop must not cost points on the new one
    $this->postJson('/hunt/reveal', ['stop_id' => $first->id])->assertStatus(409);
    expect($hunt->fresh()->currentStop()->clues_revealed)->toBe(1);
});

test('the hunt finishes after the last stop, and back and reset work', function () {
    $hunt = Hunt::current();
    $hunt->start();
    foreach (range(1, 8) as $i) {
        $hunt->advance();
    }

    expect($hunt->status())->toBe('finished')->and($hunt->total())->toBe(70);
    $hunt->advance(); // no-op
    expect($hunt->position)->toBe(8);

    $hunt->back();
    expect($hunt->status())->toBe('active')
        ->and($hunt->total())->toBe(60)
        ->and($hunt->currentStop()->title)->toBe('Spire 73');

    $hunt->reset();
    expect($hunt->status())->toBe('off')
        ->and($hunt->total())->toBe(0)
        ->and(HuntStop::where('clues_revealed', '>', 1)->count())->toBe(0);
});

test('admin can run the hunt from the control page', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/admin/hunt')->assertOk()->assertSee('Broadway Palace');
    $this->get('/admin/hunt-stops')->assertOk()->assertSee('Spire 73');
    $this->get('/admin/hunt-stops/' . HuntStop::first()->id . '/edit')->assertOk();

    Livewire::test(HuntControl::class)
        ->assertActionVisible('start')
        ->assertActionHidden('next')
        ->callAction('start')
        ->assertActionHidden('start')
        ->callAction('next')
        ->callAction('next');

    expect(Hunt::current()->position)->toBe(2)
        ->and(Hunt::current()->total())->toBe(10);

    Livewire::test(HuntControl::class)->callAction('reset');
    expect(Hunt::current()->status())->toBe('off');
});

test('admin can set and clear the message shown under the countdown', function () {
    $this->actingAs(User::factory()->create());

    $this->getJson('/hunt/state')->assertJson(['message' => null]);
    $this->get('/admin/hunt')->assertOk(); // also boots the admin panel for the Livewire tests

    Livewire::test(HuntControl::class)->callAction('message', ['message' => '  Pack an overnight bag  ']);
    $this->getJson('/hunt/state')->assertJson(['status' => 'off', 'message' => 'Pack an overnight bag']);
    $this->get('/admin/hunt')->assertOk()->assertSee('Pack an overnight bag');

    Livewire::test(HuntControl::class)->callAction('message', ['message' => '']);
    expect(Hunt::current()->message)->toBeNull();
});
