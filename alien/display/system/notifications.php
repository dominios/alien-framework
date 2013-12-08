<?

namespace Alien;

use Alien\Notification;

if (!function_exists('\Alien\renderNotification')) {

    function renderNotification(Notification $notification) {
        switch ($notification->getType()) {
            case Notification::INFO:
                $icon = 'icon-info';
                $class = 'not-info';
                $title = 'Informácia';
                break;
            case Notification::SUCCESS:
                $icon = 'icon-ok';
                $class = 'not-success';
                $title = 'Úspech';
                break;
            case Notification::WARNING:
                $icon = 'icon-warning';
                $class = 'not-warning';
                $title = 'Varovanie';
                break;
            case Notification::ERROR:
                $icon = 'icon-cancel';
                $class = 'not-error';
                $title = 'Chyba';
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

<div style="position: absolute; top: 0px; width: 100%;">
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