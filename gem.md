# Prompt: Stworzenie Komponentu Kalendarza FilamentPHP (LabaPawel/Calendar)

Jesteś ekspertem Laravel i FilamentPHP. Twoim zadaniem jest stworzenie lub modyfikacja zaawansowanego komponentu formularza (custom form component) – Kalendarza.

## Opis Funkcjonalności
Komponent ma umożliwiać wybór pojedynczej daty lub zakresu dat (start/koniec). Ma obsługiwać tryb "Double" (dwa miesiące obok siebie) oraz automatycznie mapować wybrane daty na kolumny w modelu Eloquent.

## Specyfikacja Techniczna

### 1. Klasa PHP (`Calendar.php`)
- **Dziedziczenie**: `Filament\Forms\Components\Field`.
- **Zależności**: `Carbon`.
- **Metody Konfiguracyjne**:
    - `range(bool $condition = true)`: Włącza tryb wyboru zakresu.
    - `double(bool $condition = true)`: Włącza widok dwóch miesięcy.
    - `startAttribute(string $attribute)`: Określa nazwę kolumny w bazie dla daty początkowej.
    - `endAttribute(string $attribute)`: Określa nazwę kolumny dla daty końcowej.
- **Logika Danych (Hydration/Dehydration)**:
    - **Odczyt (Hydration)**: Pobiera wartości z modelu (formatując `DateTime` do `Y-m-d`) i przekazuje do stanu komponentu.
    - **Zapis (Dehydration)**: 
        - Zapewnia formatowanie `Y-m-d` (bez czasu).
        - Wstrzykuje dane bezpośrednio do instancji modelu używając eventu `eloquent.creating`, aby obsłużyć tworzenie rekordów bez ukrytych pól formularza.

### 2. Widok Blade (`calendar.blade.php`)
- **Technologia**: Alpine.js (x-data).
- **Struktura**:
    - Kontener główny z klasą `.fi-calendar-component` i `w-full` (100% szerokości).
    - Obsługa renderowania 1 lub 2 miesięcy (`calendars[]`).
    - Nawigacja: Strzałki (poprzedni/następny miesiąc).
    - Siatka dni: Elementy `<div>` (nie `button`!), centrowane flexboxem.
- **Interakcje JS (Alpine)**:
    - `initWeekdays()`: Generowanie dni tygodnia via `Intl.DateTimeFormat` (PL).
    - `generateCalendars()`: Logika budowania siatki dni.
    - `selectDate(date)`: Obsługa logiki wyboru (start/end lub single).

### 3. Style CSS (`calendar.css`)
- **Zmienne CSS**: Użycie `--c-primary`, `--c-bg`, `--c-text` dla łatwego stylowania (wsparcie Dark Mode).
- **Layout**:
    - Komponent ma zajmować 100% szerokości kontenera.
    - Dni mają być kwadratowe (aspect-ratio: 1) i w pełni wypełnione kolorem po zaznaczeniu (nie koła).
    - Responsywność: Na mobile miesiące jeden pod drugim, na desktopie obok siebie (flex-row).

## Przykład Użycia (API)

```php
Calendar::make('rezerwacja')
    ->label('Termin pobytu')
    ->range()                       // Zakres dat
    ->double()                      // Dwa kalendarze
    ->startAttribute('data_przyjazdu') // Kolumna SQL
    ->endAttribute('data_wyjazdu')     // Kolumna SQL
    ->columnSpanFull();             // Pełna szerokość
```

## Kluczowe Wymagania
- Daty muszą być zapisywane jako czysty string `Y-m-d` (np. `2024-05-20`).
- Komponent musi działać samodzielnie, wstrzykując dane do modelu nawet jeśli pola nie są zdefiniowane w schemacie formularza Filamenta.
- Teksty (miesiące, dni) muszą pochodzić z `Intl` przeglądarki (niezależnie od plików tłumaczęń PHP).
