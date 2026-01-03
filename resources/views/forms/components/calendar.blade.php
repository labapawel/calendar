<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>

    <div
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}'),
            startAttribute: @js($getStartAttribute()),
            endAttribute: @js($getEndAttribute()),
            isRange: @js($isRange()),
            isDouble: @js($isDouble()),
            month: new Date().getMonth(),
            year: new Date().getFullYear(),
            calendars: [],
            weekdays: [],
            
            init() {
                this.initWeekdays();
                this.generateCalendars();
                this.$watch('state', () => {});
            },

            initWeekdays() {
                const format = new Intl.DateTimeFormat('pl-PL', { weekday: 'short' });
                // Generate based on a known week containing a Monday (Jan 1 2024 is Monday)
                this.weekdays = [...Array(7).keys()].map(i => {
                    const d = new Date(2024, 0, 1 + i);
                    return format.format(d).replace('.', ''); // Remove trailing dots if any
                });
            },

            getMonthName(year, month) {
                const d = new Date(year, month, 1);
                return new Intl.DateTimeFormat('pl-PL', { month: 'long', year: 'numeric' }).format(d);
            },

            generateCalendars() {
                const count = this.isDouble ? 2 : 1;
                let calendars = [];
                
                for (let i = 0; i < count; i++) {
                    let month = this.month + i;
                    let year = this.year;
                    
                    if (month > 11) {
                        month -= 12;
                        year++;
                    }

                    const firstDay = new Date(year, month, 1);
                    const lastDay = new Date(year, month + 1, 0);
                    // Adjust start day to Monday (0=Sun -> 6, 1=Mon -> 0)
                    let startingDay = firstDay.getDay() - 1;
                    if (startingDay < 0) startingDay = 6;
                    
                    const info = { daysInMonth: lastDay.getDate(), startingDay: startingDay };
                    
                    let days = [];
                    for (let j = 0; j < info.startingDay; j++) days.push({ day: '', disabled: true });
                    
                    for (let j = 1; j <= info.daysInMonth; j++) {
                        const d = new Date(year, month, j);
                        // Safe date formatting
                        const y = d.getFullYear();
                        const m = String(d.getMonth() + 1).padStart(2, '0');
                        const day = String(d.getDate()).padStart(2, '0');
                        days.push({ day: j, date: `${y}-${m}-${day}`, disabled: false });
                    }
                    
                    // Generate title dynamically
                    const title = this.getMonthName(year, month);
                    
                    calendars.push({ month, year, days, title });
                }
                this.calendars = calendars;
            },

            prevMonth() {
                this.month--;
                // Handle year wrap for internal state
                if (this.month < 0) { 
                    this.month = 11; 
                    this.year--; 
                }
                this.generateCalendars();
            },

            nextMonth() {
                this.month++;
                if (this.month > 11) { 
                    this.month = 0; 
                    this.year++; 
                }
                this.generateCalendars();
            },

            isSelected(date) {
                if (!this.state) return false;
                if (this.startAttribute && this.endAttribute && typeof this.state === 'object') {
                    const start = this.state.start;
                    const end = this.state.end;
                    if (!start) return false;
                    if (this.isRange) return date >= start && date <= (end || start);
                    return date === start;
                }
                return this.state === date;
            },

            selectDate(date) {
                if (this.startAttribute && this.endAttribute) {
                    let currentState = this.state || {};
                    if (typeof currentState !== 'object' || currentState === null) currentState = {};
                    if (this.isRange) {
                        if (!currentState.start || (currentState.start && currentState.end)) {
                            currentState.start = date;
                            currentState.end = null;
                        } else {
                            if (date < currentState.start) {
                                currentState.end = currentState.start;
                                currentState.start = date;
                            } else {
                                currentState.end = date;
                            }
                        }
                    } else {
                        currentState.start = date;
                        currentState.end = null;
                    }
                    this.state = currentState;
                } else {
                    this.state = date;
                }
            }
        }"
        class="fi-calendar-component w-full"
        x-cloak
    >


        <div class="fi-cal-months-container" :class="{ 'is-double': isDouble }">
            <template x-for="(cal, calIndex) in calendars" :key="calIndex">
                <div class="fi-cal-month">
                    <!-- Header -->
                    <div class="fi-cal-header">
                        <div class="w-8">
                             <!-- Show Prev only on first calendar -->
                            <button type="button" x-show="calIndex === 0" @click="prevMonth()" class="fi-cal-btn-nav" aria-label="Previous Month">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            </button>
                        </div>
                        
                        <!-- Use pre-calculated dynamic title -->
                        <div class="fi-cal-title" x-text="cal.title"></div>
                        
                        <div class="w-8 text-right">
                             <!-- Show Next only on last calendar -->
                            <button type="button" x-show="calIndex === calendars.length - 1" @click="nextMonth()" class="fi-cal-btn-nav ml-auto" aria-label="Next Month">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Weekdays -->
                    <div class="fi-cal-grid mb-2">
                        <template x-for="day in weekdays">
                            <div x-text="day" class="fi-cal-weekday"></div>
                        </template>
                    </div>

                    <!-- Days -->
                    <div class="fi-cal-grid">
                        <template x-for="(dayObj, index) in cal.days" :key="index">
                            <div 
                                x-text="dayObj.day"
                                @click="dayObj.day && selectDate(dayObj.date)"
                                :class="{
                                    'is-selected': isSelected(dayObj.date),
                                    'invisible': !dayObj.day
                                }"
                                class="fi-cal-day"
                            ></div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>    
        <!-- Debug Info (Optional, can remove later) -->
        <div class="mt-4 text-xs text-gray-400" x-show="isRange">
             <span x-text="startAttribute ? 'Mapped Mode' : 'Simple Mode'"></span>
        </div>
    </div>
</x-dynamic-component>
