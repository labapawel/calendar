<?php

namespace LabaPawel\Calendar\Forms\Components;

use Filament\Forms\Components\Field;

class Calendar extends Field
{
    protected string $view = 'calendar::forms.components.calendar';

    protected bool $isRange = false;
    protected bool $isDouble = false;
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
                $start = $record->getAttribute($this->startAttribute);
                $end = $record->getAttribute($this->endAttribute);

                // Ensure strict Y-m-d format for frontend
                if ($start) {
                    $start = \Illuminate\Support\Carbon::parse($start)->format('Y-m-d');
                }
                if ($end) {
                    $end = \Illuminate\Support\Carbon::parse($end)->format('Y-m-d');
                }

                $component->state([
                    'start' => $start,
                    'end' => $end,
                ]);
            }
        });

        $this->dehydrateStateUsing(function (Calendar $component, $state, $record) {
            // Check if mapping is active
            if ($this->startAttribute && $this->endAttribute) {
                // Determine values from state (array or string)
                $start = null;
                $end = null;

                if (is_array($state)) {
                    $start = $state['start'] ?? null;
                    $end = $state['end'] ?? null;
                } elseif (is_string($state)) {
                    $start = $state;
                    $end = null;
                }

                // Format dates safely
                if ($start) {
                    try {
                        $start = \Illuminate\Support\Carbon::parse($start)->startOfDay()->toDateString();
                    } catch (\Exception $e) {}
                }
                if ($end) {
                    try {
                        $end = \Illuminate\Support\Carbon::parse($end)->startOfDay()->toDateString();
                    } catch (\Exception $e) {}
                }

                // Strategy 1: Update Record (Edit Mode)
                if ($record) {
                    $record->setAttribute($this->startAttribute, $start);
                    $record->setAttribute($this->endAttribute, $end);
                }

                // Strategy 2: Inject into Model (Create Mode) via Event Listener
                // This bypasses Filament's schema filtering by interacting with the Model instance directly before save.
                if (! $record) {
                     $modelClass = $component->getModel();
                     if ($modelClass) {
                         \Illuminate\Support\Facades\Event::listen(
                             "eloquent.creating: {$modelClass}",
                             function ($model) use ($start, $end) {
                                 // Only act if attributes are not already set (safety)
                                 if ($this->startAttribute && ! $model->getAttribute($this->startAttribute)) {
                                     $model->setAttribute($this->startAttribute, $start);
                                 }
                                 if ($this->endAttribute && ! $model->getAttribute($this->endAttribute)) {
                                     $model->setAttribute($this->endAttribute, $end);
                                 }
                             }
                         );
                     }
                }

                // Update internal state to match formatted dates
                if (is_array($state)) {
                    $state['start'] = $start;
                    $state['end'] = $end;
                } else {
                    $state = $start;
                }
            } elseif (is_string($state) && ! empty($state)) {
                // Simple Mode: Just format the state itself
                try {
                    return \Illuminate\Support\Carbon::parse($state)->startOfDay()->toDateString();
                } catch (\Exception $e) {
                    return $state;
                }
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

    public function double(bool $condition = true): static
    {
        $this->isDouble = $condition;
        return $this;
    }

    public function isDouble(): bool
    {
        return $this->isDouble;
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
