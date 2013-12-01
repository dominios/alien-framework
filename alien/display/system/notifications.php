<?

namespace Alien;

use Alien\Notification;
?>

<div style="position: absolute; top: 0px; width: 100%;">
    <div id="notifyArea" style="display: none; width: 500px;">
        <?
        foreach ($this->List as $note):
            switch ($note->getType()) {
                case Notification::INFO:
                    echo ('<div class="notification information"><img src="' . Alien::$SystemImgUrl . '/information.png">&nbsp;' . $note->getMessage() . '</div>');
                    break;
                case Notification::SUCCESS:
                    echo ('<div class="notification success"><img src="' . Alien::$SystemImgUrl . '/tick.png">&nbsp;' . $note->getMessage() . '</div>');
                    break;
                case Notification::WARNING:
                    echo ('<div class="notification warning"><img src="' . Alien::$SystemImgUrl . '/warning.png">&nbsp;' . $note->getMessage() . '</div>');
                    break;
                case Notification::ERROR:
                    echo ('<div class="notification error"><img src="' . Alien::$SystemImgUrl . '/cross.png">&nbsp;' . $note->getMessage() . '</div>');
                    break;
            }
        endforeach;
        ?>
        <script type="text/javascript"> $(document).ready(function() {
                showNotifications();
            });</script>
    </div>
</div>