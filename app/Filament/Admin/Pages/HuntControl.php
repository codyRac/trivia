<?php

namespace App\Filament\Admin\Pages;

use App\Models\Hunt;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class HuntControl extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Scavenger Hunt';

    protected static ?string $title = 'Scavenger Hunt';

    protected static ?string $slug = 'hunt';

    protected static ?int $navigationSort = -1;

    protected string $view = 'filament.admin.pages.hunt-control';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('start')
                ->label('Start the hunt')
                ->icon('heroicon-o-play')
                ->color('success')
                ->size('lg')
                ->visible(fn () => Hunt::current()->status() === 'off')
                ->requiresConfirmation()
                ->modalDescription('She will see the intro message right away. The first clue appears when you press Next.')
                ->action(function () {
                    Hunt::current()->start();
                    Notification::make()->title('The hunt is on!')->success()->send();
                }),

            Action::make('next')
                ->label(fn () => $this->nextLabel())
                ->icon('heroicon-o-forward')
                ->color('primary')
                ->size('lg')
                ->visible(fn () => in_array(Hunt::current()->status(), ['intro', 'active']))
                ->requiresConfirmation()
                ->modalDescription(fn () => $this->nextDescription())
                ->action(function () {
                    Hunt::current()->advance();
                    Notification::make()->title('Moved to the next stage')->success()->send();
                }),

            Action::make('back')
                ->label('Go back one')
                ->icon('heroicon-o-backward')
                ->color('gray')
                ->visible(fn () => in_array(Hunt::current()->status(), ['active', 'finished']))
                ->requiresConfirmation()
                ->modalDescription('Undoes the last "Next". Clues she already revealed stay revealed.')
                ->action(fn () => Hunt::current()->back()),

            Action::make('reset')
                ->label('Reset')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->visible(fn () => Hunt::current()->status() !== 'off')
                ->requiresConfirmation()
                ->modalHeading('Reset the hunt?')
                ->modalDescription('Turns the hunt off and wipes all points and revealed clues. Use this after a test run.')
                ->action(function () {
                    Hunt::current()->reset();
                    Notification::make()->title('Hunt reset and turned off')->success()->send();
                }),
        ];
    }

    protected function getViewData(): array
    {
        $hunt = Hunt::current();

        return [
            'hunt' => $hunt,
            'status' => $hunt->status(),
            'stops' => $hunt->stops(),
            'current' => $hunt->currentStop(),
            'total' => $hunt->total(),
        ];
    }

    private function nextLabel(): string
    {
        $hunt = Hunt::current();

        return match (true) {
            $hunt->status() === 'intro' => 'Next: show Stop 1',
            $hunt->position >= $hunt->stops()->count() => 'Finish the hunt',
            default => 'Next: Stop ' . ($hunt->position + 1),
        };
    }

    private function nextDescription(): string
    {
        $stop = Hunt::current()->currentStop();

        return $stop
            ? "She gets {$stop->worth()} points for {$stop->title}."
            : 'She will see the first clue.';
    }
}
