<?php

namespace Leandrocfe\FilamentPtbrFormFields;

use ArchTech\Money\Currency;
use Closure;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use Leandrocfe\FilamentPtbrFormFields\Currencies\BRL;

class Money extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->prefix('R$');

        $this->extraAttributes([
            'x-on:keypress' => 'function() {
                var charCode = event.keyCode || event.which;
                if (charCode < 48 || charCode > 57) {
                    event.preventDefault();
                    return false;
                }
                return true;
            }',
            'x-on:keyup' => 'function() {
                var money = $el.value.replace(/\D/g, "");
                money = (money / 100).toFixed(2) + "";
                money = money.replace(".", ",");
                money = money.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
                money = money.replace(/(\d)(\d{3}),/g, "$1.$2,");
                $el.value = money;
            }',
        ], true);

        $this->dehydrateStateUsing(function ($state) {
            return $state ? (float) Str::of($state)->replace('.', '')->replace(',', '.')->toString() : null;
        });

        $this->formatStateUsing(function ($state) {
            return $state ? number_format((float) $state, 2, ',', '.') : '0,00';
        });
    }
}
