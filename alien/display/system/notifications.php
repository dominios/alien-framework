<?

namespace Alien;

use Alien\Notification;

if (!function_exists('\Alien\renderNotification')) {

    function renderNotification(Notification $notification) {
        switch ($notification->getType()) {
            case Notification::INFO:
                $icon = 'icon-notify-info';
                $class = 'not-info';
                $title = 'Informácia';
                break;
            case Notification::NEW_MESSAGES:
                $icon = 'icon-notify-messages';
                $class = 'not-info';
                $title = 'Informácia';
                break;
            case Notification::SUCCESS:
                $icon = 'icon-notify-ok';
                $class = 'not-success';
                $title = 'Úspech';
                break;
            case Notification::WARNING:
                $icon = 'icon-notify-warning';
                $class = 'not-warning';
                $title = 'Varovanie';
                break;
            case Notification::ERROR:
                $icon = 'icon-notify-error';
                $class = 'not-error';
                $title = 'Chyba';
                break;
            case Notification::ACCESS_DENIED:
                $icon = 'icon-notify-private';
                $class = 'not-error';
                $title = 'Nepovolený prístup';
                break;
        }
        return ('<div class="notify ' . $class . '"><span class="icon ' . $icon . '"></span>' . '<b>' . $title . ':</b> ' . $notification->getMessage() . '</div>');
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
            $(function() {
                $("#notifyArea").addClass('visible');
                setTimeout(function() {
                    $('#notifyArea').removeClass("visible");
                }, 4000);
            });
        </script>
    </div>
</div>