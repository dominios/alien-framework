<div class="row">
    <div class="col-xs-12">
        <?= $this->addButtton; ?>
    </div>
</div>

<div class="space-15"></div>

<div class="row">
    <div class="col-xs-12">
        <div id="calendar"></div>
    </div>
</div>

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
            axisFormat: 'H:mm',
            minTime: '07:00:00',
            maxTime: '22:00:00',
            allDaySlot: false,
            editable: false,
            eventLimit: false,
            events: <?= json_encode($this->events); ?>,
            eventClick: function (event) {
                if (event.url) {
                    window.open(event.url);
                    return false;
                }
            }
        });

    });
</script>