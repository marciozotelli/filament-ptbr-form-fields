<?php

namespace Leandrocfe\FilamentPtbrFormFields;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Livewire\Component as Livewire;

class Cep extends TextInput
{
    public function viaCep(string $mode = 'suffix', string $errorMessage = 'CEP inválido.', array $setFields = []): static
    {
        $viaCepRequest = function ($state, Livewire $livewire, Set $set, Component $component) use ($errorMessage, $setFields) {

            $livewire->validateOnly($component->getKey());

            $request = Http::get(config('filament-ptbr-form-fields.viacep_url') . $state . '/json/')->json();

            if (blank($request) || Arr::has($request, 'erro')) {
                throw ValidationException::withMessages([
                    $component->getKey() => $errorMessage,
                ]);
            }

            foreach ($setFields as $key => $value) {
                $set($key, $request[$value] ?? null);
            }
        };

        $this
            ->minLength(9)
            ->mask('99999-999')
            ->afterStateUpdated(function ($state, Livewire $livewire, Set $set, Component $component) use ($viaCepRequest) {
                $viaCepRequest($state, $livewire, $set, $component);
            });

        $action = Action::make('search-action')
            ->label('Buscar CEP')
            ->icon('heroicon-o-magnifying-glass')
            ->action(function ($state, Livewire $livewire, Set $set, Component $component) use ($viaCepRequest) {
                $viaCepRequest($state, $livewire, $set, $component);
            });

        if ($mode === 'suffix') {
            $this->suffix($action);
        } else {
            $this->prefix($action);
        }


        return $this;
    }
}
