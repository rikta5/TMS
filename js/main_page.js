document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    // Generate random events
    function generateRandomEvents() {
        var events = [];
        var eventTitles = ["Business Meeting", "Party", "Conference", "Workshop", "Webinar"];
        var colors = ['#8ca0ff', '#ff6363', '#ff8c42', '#4CAF50', '#2196F3'];

        for (var i = 0; i < 5; i++) {
            var randomTitle = eventTitles[Math.floor(Math.random() * eventTitles.length)];
            var randomColor = colors[Math.floor(Math.random() * colors.length)];
            var randomDate = new Date();
            randomDate.setDate(randomDate.getDate() + Math.floor(Math.random() * 30)); // Random date within next 30 days

            // Generate random start and end times between 9 AM and 5 PM
            var startHour = Math.floor(Math.random() * (17 - 9)) + 9; // Random hour between 9 and 16
            var startMinute = Math.floor(Math.random() * 60); // Random minute
            var endHour = startHour + Math.floor(Math.random() * (17 - startHour - 1)) + 1; // Random end hour after start hour
            var endMinute = Math.floor(Math.random() * 60); // Random end minute

            var startDate = randomDate.toISOString().split('T')[0];
            var endDate = new Date(randomDate);
            endDate.setHours(endHour, endMinute);

            events.push({
                title: randomTitle,
                start: `${startDate}T${String(startHour).padStart(2, '0')}:${String(startMinute).padStart(2, '0')}:00`,
                end: `${startDate}T${String(endHour).padStart(2, '0')}:${String(endMinute).padStart(2, '0')}:00`,
                color: randomColor
            });
        }

        return events;
    }

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: generateRandomEvents()
    });

    calendar.render();
});
