<?php

namespace LabaPawel\Calendar\Forms\Components;

use Filament\Forms\Components\Field;

class Calendar extends Field
{
    protected string $view = 'calendar::forms.components.calendar';

    protected bool $isRange = false;
    protected ?string $startAttribute = null;
    protected ?string $endAttribute = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (Calendar $component, $record) {
            if (! $record) {
                return;
            }

            if ($this->startAttribute && $this->endAttribute) {
                $component->state([
                    'start' => $record->getAttribute($this->startAttribute),
                    'end' => $record->getAttribute($this->endAttribute),
                ]);
            }
        });

        $this->dehydrateStateUsing(function (Calendar $component, $state, $record) {
            if (! $record) {
                return $state;
            }

            if ($this->startAttribute && $this->endAttribute && is_array($state)) {
                $record->setAttribute($this->startAttribute, $state['start'] ?? null);
                $record->setAttribute($this->endAttribute, $state['end'] ?? null);
            }

            return $state;
        });
    }

    public function range(bool $condition = true): static
    {
        $this->isRange = $condition;
        return $this;
    }

    public function isRange(): bool
    {
        return $this->isRange;
    }

    public function startAttribute(string $attribute): static
    {
        $this->startAttribute = $attribute;
        return $this;
    }

    public function endAttribute(string $attribute): static
    {
        $this->endAttribute = $attribute;
        return $this;
    }

    public function getStartAttribute(): ?string
    {
        return $this->startAttribute;
    }

    public function getEndAttribute(): ?string
    {
        return $this->endAttribute;
    }
}
