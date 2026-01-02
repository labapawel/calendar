# Filament Calendar Component

A custom calendar component for FilamentPHP, supporting single date and date range selection.

## Installation

You can install the package via composer. Since this is a local package under development, add it to your `composer.json` repositories:

```json
"repositories": [
    {
        "type": "path",
        "url": "../path/to/calendar"
    }
]
```

Then require it:

```bash
composer require labapawel/calendar:@dev
```

## Configuration

Optionally, you can publish the views using:

```bash
php artisan vendor:publish --tag="calendar-views"
```

## Usage

In your Filament Resource or Form:

```php
use LabaPawel\Calendar\Forms\Components\Calendar;

Calendar::make('appointment_date')
    ->label('Appointment Date')
    ->range(false) // Set to true for range selection
```

### Range Selection

To enable range selection (two calendars):

```php
Calendar::make('booking_period')
    ->range()
```

```php
Calendar::make('booking_period')
    ->range()
```

### Model Field Mapping

You can map the start and end dates to specific attributes on your model. This is useful when your database table has separate columns for start and ends dates (e.g., `started_at` and `ended_at`), rather than a single JSON column.

```php
Calendar::make('dataset') // The name here doesn't matter much if mapping fields, but should be unique
    ->range()
    ->startAttribute('started_at')
    ->endAttribute('ended_at')
```

## Localization

The package supports English and Polish. You can publish translations if needed (currently loaded automatically).

To customize translations, you can create files in `resources/lang/vendor/calendar/`.
