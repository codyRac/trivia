<?php

use App\Filament\Admin\Widgets\FlagStats;
use App\Models\Flag;
use App\Models\FlagDay;
use App\Models\User;
use Database\Seeders\FlagSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(FlagSeeder::class);
});

test('visiting the flag game collects a new flag for today', function () {
    $this->get('/flags')->assertOk();

    expect(FlagDay::count())->toBe(1)
        ->and(Flag::whereNotNull('learned_order')->count())->toBe(1);
});

test('admin flag pages render', function () {
    $this->get('/flags');
    $user = User::factory()->create();
    $flag = FlagDay::first()->flag;

    $this->actingAs($user)->get('/admin')->assertOk();
    $this->actingAs($user)->get('/admin/flags')->assertOk()->assertSee($flag->name);
    $this->actingAs($user)->get('/admin/flag-days')->assertOk()->assertSee($flag->name);
    $this->actingAs($user)->get("/admin/flags/{$flag->id}/edit")->assertOk();
});

test('flag stats widget shows collection progress', function () {
    $this->get('/flags');

    Livewire::test(FlagStats::class)
        ->assertSee('Flags Collected')
        ->assertSee('1 / 197')
        ->assertSee('In progress (0/1)');
});
