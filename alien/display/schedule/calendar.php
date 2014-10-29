<div id="calendar"></div>

<script type="text/javascript">

    $(document).ready(function () {

        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            lang: 'sk',
            defaultDate: '<?= date('Y-m-d', time()); ?>',
            defaultView: 'agendaWeek',
            editable: false,
            eventLimit: false,
            events: [
                {
                    title: 'Kurz AJ',
                    start: '2014-10-29T09:00:00',
                    end: '2014-10-29T10:30:00'
                },
                {
                    title: 'Kurz NJ',
                    start: '2014-10-29T11:00:00',
                    end: '2014-10-29T12:30:00'
                }
            ]
        });

    });
</script>