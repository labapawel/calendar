<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>aaaaaa
    <div
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}'),
            startAttribute: @js($getStartAttribute()),
            endAttribute: @js($getEndAttribute()),
            isRange: @js($isRange()),
            month: new Date().getMonth(),
            year: new Date().getFullYear(),
            days: [],
            weekdays: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            
            init() {
                this.generateCalendar();
                
                // Watch for external state changes to update calendar view if needed
                this.$watch('state', value => {
                    // Logic to jump to selected date could go here
                });
            },

            generateCalendar() {
                const firstDay = new Date(this.year, this.month, 1);
                const lastDay = new Date(this.year, this.month + 1, 0);
                const startingDay = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1; // Adjust for Monday start
                const info = {
                    daysInMonth: lastDay.getDate(),
                    startingDay: startingDay
                };

                let days = [];
                
                // Previous month padding
                for (let i = 0; i < info.startingDay; i++) {
                     days.push({ day: '', disabled: true });
                }

                // Days of current month
                for (let i = 1; i <= info.daysInMonth; i++) {
                    days.push({ 
                        day: i, 
                        date: new Date(this.year, this.month, i).toISOString().split('T')[0],
                        disabled: false 
                    });
                }

                this.days = days;
            },

            prevMonth() {
                this.month--;
                if (this.month < 0) {
                    this.month = 11;
                    this.year--;
                }
                this.generateCalendar();
            },

            nextMonth() {
                this.month++;
                if (this.month > 11) {
                    this.month = 0;
                    this.year++;
                }
                this.generateCalendar();
            },

            isSelected(date) {
                if (!this.state) return false;
                
                // Handle mapped attributes (object state)
                if (this.startAttribute && this.endAttribute && typeof this.state === 'object') {
                    const start = this.state.start;
                    const end = this.state.end;
                    if (!start) return false;
                    
                    if (this.isRange) {
                        return date >= start && date <= (end || start);
                    }
                    return date === start;
                }

                // Handle simple state
                if (this.isRange && Array.isArray(this.state)) {
                     // Basic array range implementation if needed
                     return false; 
                }

                return this.state === date;
            },

            selectDate(date) {
                if (this.startAttribute && this.endAttribute) {
                    let currentState = this.state || {};
                    if (typeof currentState !== 'object') currentState = {};

                    if (this.isRange) {
                        if (!currentState.start || (currentState.start && currentState.end)) {
                            // Start new selection
                            currentState.start = date;
                            currentState.end = null;
                        } else {
                            // Complete selection
                            if (date < currentState.start) {
                                currentState.end = currentState.start;
                                currentState.start = date;
                            } else {
                                currentState.end = date;
                            }
                        }
                    } else {
                        currentState.start = date;
                        currentState.end = null; // Clear end if switching to single? Or just ignore.
                    }
                    this.state = currentState;
                } else {
                    // Simple string state
                    this.state = date;
                }
            }
        }"
        class="w-full text-sm rounded-lg shadow-sm border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"
    >
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <button type="button" @click="prevMonth()" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                &larr;
            </button>
            <div x-text="monthNames[month] + ' ' + year" class="font-bold text-lg"></div>
            <button type="button" @click="nextMonth()" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                &rarr;
            </button>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-7 gap-1 text-center mb-2">
            <template x-for="day in weekdays">
                <div x-text="day" class="text-xs font-medium text-gray-500 uppercase"></div>
            </template>
        </div>

        <div class="grid grid-cols-7 gap-1">
            <template x-for="(dayObj, index) in days" :key="index">
                <div>
                     <template x-if="dayObj.day">
                        <button 
                            type="button"
                            x-text="dayObj.day"
                            @click="selectDate(dayObj.date)"
                            :class="{
                                'bg-primary-500 text-white': isSelected(dayObj.date),
                                'hover:bg-gray-100 dark:hover:bg-gray-700': !isSelected(dayObj.date)
                            }"
                            class="w-8 h-8 rounded-full flex items-center justify-center mx-auto transition-colors"
                        ></button>
                     </template>
                </div>
            </template>
        </div>
        
        <!-- Debug Info (Optional, can remove later) -->
        <div class="mt-4 text-xs text-gray-400" x-show="isRange">
             <span x-text="startAttribute ? 'Mapped Mode' : 'Simple Mode'"></span>
        </div>
    </div>
</x-dynamic-component>
