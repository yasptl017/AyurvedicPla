<?php

namespace App\Filament\App\Resources\Patients\Resources\PatientHistories\Schemas;

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Illuminate\Support\HtmlString;

class AstavidhyaParikshaForm
{
    /**
     * Render a bold label with an inline reset button.
     * The reset button finds all radio inputs inside the nearest fi-fo-radio
     * wrapper that shares the given field key, unchecks them, and fires a
     * change event so Livewire picks up the null value.
     */
    private static function labelWithReset(string $text, string $fieldKey): HtmlString
    {
        $label = e($text);
        $key = e($fieldKey);

        return new HtmlString(
            '<span style="display:flex;align-items:center;gap:8px;">'
            .'<strong>'.$label.'</strong>'
            .'<button type="button"'
            .' x-on:click="'
            ."let wrap = \$el.closest('[wire\\\\:id]');"
            .'if (!wrap) return;'
            ."wrap.querySelectorAll('input[type=radio][name*={$key}]').forEach(function(r){"
            .'  r.checked = false;'
            ."  r.dispatchEvent(new Event('change',{bubbles:true}));"
            .'});'
            .'"'
            .' style="font-size:10px;padding:1px 7px;border:1px solid #d1d5db;border-radius:999px;'
            .'background:#f9fafb;color:#6b7280;cursor:pointer;line-height:1.6;white-space:nowrap;"'
            .'>reset</button>'
            .'</span>'
        );
    }

    public static function configure(): Group
    {
        return Group::make()
            ->relationship('astavidhyaPariksha')
            ->schema([
                Section::make('અષ્ટવિધ્ય પરિક્ષા')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Radio::make('Nadi')
                                    ->label(self::labelWithReset('નાડી', 'Nadi'))
                                    ->options([
                                        'vat' => 'વાત – ↑ HR ↓ Volume',
                                        'pit' => 'પિત – ↑ HR ↑ Volume',
                                        'kaf' => 'કફ – ↓ HR ↑ Volume',
                                        'kshina' => 'ક્ષીણ',
                                        'aam' => 'આમ',
                                    ])
                                    ->columnSpanFull(),

                                Radio::make('Mal')
                                    ->label(self::labelWithReset('મલ', 'Mal'))
                                    ->options([
                                        'soft' => 'Soft',
                                        'hard' => 'Hard',
                                        'ibs' => 'IBS',
                                    ]),

                                Radio::make('Mutra')
                                    ->label(self::labelWithReset('મુત્ર', 'Mutra'))
                                    ->options([
                                        'samyak' => 'સમ્યક',
                                        'alpa' => 'અલ્પ',
                                        'vadhare' => 'વધારે',
                                    ]),

                                Radio::make('Jihva')
                                    ->label(self::labelWithReset('જીહવા', 'Jihva'))
                                    ->options([
                                        'saam' => 'સામ',
                                        'niraam' => 'નિરામ',
                                    ]),

                                Radio::make('Sparsha')
                                    ->label(self::labelWithReset('સ્પર્શ', 'Sparsha'))
                                    ->options([
                                        'ushna' => 'ઉષ્ણ',
                                        'sheet' => 'શીત',
                                    ]),

                                Radio::make('Kshudha')
                                    ->label(self::labelWithReset('ક્ષુધા', 'Kshudha'))
                                    ->options([
                                        'sam' => 'શમ',
                                        'visham' => 'વિષમ',
                                        'tikshna' => 'તીક્ષ્ણ',
                                        'mand' => 'મંદ',
                                    ]),

                                Radio::make('Nindra')
                                    ->label(self::labelWithReset('નિંદ્રા', 'Nindra'))
                                    ->options([
                                        'samyak' => 'સમ્યક',
                                        'madhyam' => 'મધ્યમ',
                                        'alpa' => 'અલ્પ',
                                    ]),
                            ]),

                        Fieldset::make('આર્તવ')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Radio::make('AartavRegular')
                                            ->label(self::labelWithReset('Regular', 'AartavRegular'))
                                            ->options([
                                                'scanty' => 'Scanty',
                                                'moderate' => 'Moderate',
                                                'excessive' => 'Excessive',
                                            ]),

                                        Radio::make('AartavIrregular')
                                            ->label(self::labelWithReset('Irregular', 'AartavIrregular'))
                                            ->options([
                                                'scanty' => 'Scanty',
                                                'moderate' => 'Moderate',
                                                'excessive' => 'Excessive',
                                            ]),
                                    ]),
                            ]),

                        Textarea::make('Remark')
                            ->label('Remark')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
