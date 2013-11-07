<form method="POST" id="messageForm">
    <section class="tabs" id="messageTabs">
        <header>
            <ul>
                <li class="active"><a href="#form"><span class="icon icon-message-out"></span>Nová správa</a></li>
            </ul>
        </header>
        <section>
            <article id="form">
                <input type="hidden" name="action" value="dashboard/sendMessage">
                Prijímateľ: <input type="text" name="messageRecipient" value="<?= (($this->recipient !== null) ? $this->recipient->getId() : ''); ?>" placeholder="ID prijímateľa">
                Text správy:
                <textarea name="messageText"></textarea>
                <div class="button negative" onclick="javascript: window.location = '<?= $this->returnAction; ?>';"><span class="icon icon-back"></span>Zrušiť</div>
                <div class="button positive" onClick="javascript: $('#messageForm').submit();"><span class="icon icon-message"></span>Odoslať</div>

            </article>
        </section>
    </section>

</form>