<?

namespace Alien;

use Alien\Notification;

if (!function_exists('\Alien\renderNotification')) {

    function renderNotification(Notification $notification) {
        switch ($notification->getType()) {
            case Notification::INFO:
                $icon = 'fa fa-fw fa-info-circle';
                $class = 'alert-info';
                $title = 'Informácia';
                break;
            case Notification::NEW_MESSAGES:
                $icon = 'fa fa-fw fa-info-circle';
                $class = 'alert-info';
                $title = 'Informácia';
                break;
            case Notification::SUCCESS:
                $icon = 'fa fa-fw fa-check-circle';
                $class = 'alert-success';
                $title = 'Úspech';
                break;
            case Notification::WARNING:
                $icon = 'fa fa-fw fa-warning';
                $class = 'alert-warning';
                $title = 'Varovanie';
                break;
            case Notification::ERROR:
                $icon = 'fa fa-fw fa-exclamation-circle';
                $class = 'alert-danger';
                $title = 'Chyba';
                break;
            case Notification::ACCESS_DENIED:
                $icon = 'fa fa-fw fa-exclamation-circle';
                $class = 'alert-danger';
                $title = 'Nepovolený prístup';
                break;
        }
        return ('<div class="alert ' . $class . '"><i class="fa ' . $icon . '"></i>' . '<b>' . $title . ':</b> ' . $notification->getMessage() . '</div>');
    }

}
?>

<?
if (!$this instanceof \Alien\View) {
    return;
}
?>

<div style="position: absolute; top: 6px; right: 6px;">
    <div id="notifyArea">
        <?
        foreach ($this->list as $note):

            if (!($note instanceof Notification)):
                continue;
            endif;

            echo renderNotification($note);

        endforeach;
        ?>
        <script type="text/javascript">
            $(function () {
                $("#notifyArea").addClass('visible');
                setTimeout(function () {
                    $('#notifyArea').removeClass("visible");
                }, 4000);
            });
        </script>
    </div>
</div>