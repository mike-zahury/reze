<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Kalendář dostupnosti vozidel</title>
    <link rel="stylesheet" href="styles.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales-all.min.js'></script>
    <style>
        .fc-event {
            border: 1px solid #fff;
            color: #fff;
            padding: 5px;
            border-radius: 3px;
            font-size: 0.9em;
        }
        .fc-event:hover {
            opacity: 0.8;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'cs',
                initialView: 'dayGridMonth',
                events: 'fetch_reservations.php',
                eventMouseEnter: function(info) {
                    var tooltip = new Tooltip(info.el, {
                        title: info.event.extendedProps.description,
                        placement: 'top',
                        trigger: 'hover',
                        container: 'body'
                    });
                },
                eventMouseLeave: function(info) {
                    if (info.el._tippy) {
                        info.el._tippy.destroy();
                    }
                }
            });

            calendar.render();
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>Kalendář dostupnosti vozidel</h1>
        <div id='calendar'></div>
        <a href="index.php" class="btn">Zpět na rezervace</a>
    </div>
</body>
</html>