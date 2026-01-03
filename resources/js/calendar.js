// Calendar logic definition
document.addEventListener('alpine:init', () => {
    Alpine.data('calendar', ({ state, startAttribute, endAttribute, isRange }) => ({
        state,
        startAttribute,
        endAttribute,
        isRange,
        month: new Date().getMonth(),
        year: new Date().getFullYear(),
        days: [],
        weekdays: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        
        init() {
            this.generateCalendar();
            
            this.$watch('state', value => {
                // Logic to update view from state if needed
            });
        },

        generateCalendar() {
            const firstDay = new Date(this.year, this.month, 1);
            const lastDay = new Date(this.year, this.month + 1, 0);
            const startingDay = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1; 
            const info = {
                daysInMonth: lastDay.getDate(),
                startingDay: startingDay
            };

            let days = [];
            
            for (let i = 0; i < info.startingDay; i++) {
                    days.push({ day: '', disabled: true });
            }

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
            
            if (this.startAttribute && this.endAttribute && typeof this.state === 'object') {
                const start = this.state.start;
                const end = this.state.end;
                if (!start) return false;
                
                if (this.isRange) {
                    return date >= start && date <= (end || start);
                }
                return date === start;
            }

            if (this.isRange && Array.isArray(this.state)) {
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
    }));
});
