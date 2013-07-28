<div style="position: absolute; top: 0px; width: 100%;">
    <div id="notifyArea" style="display: none; width: 500px;">
        <?=Notification::renderNotifications();?>
        <script type="text/javascript"> $(document).ready(function(){ showNotifications(); }); </script>
    </div>
</div>