<?php

namespace App\Filament;

use Filament\Forms\Components\Field;

class Sketch extends Field
{
    protected string $view = 'forms.components.sketch-pad';

    // Optional: Allow configuring height
    protected int|string|null $height = 400;

    public function height(int|string|null $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getHeight(): int|string
    {
        return $this->height ?? 400;
    }

}
