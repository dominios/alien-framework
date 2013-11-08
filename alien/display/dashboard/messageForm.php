<form method="POST" id="messageForm">
    <input type="hidden" name="action" value="dashboard/sendMessage">
    <section class="tabs" id="messageTabs">
        <header>
            <ul>
                <li class="active"><a href="#form"><span class="icon icon-message-out"></span>Nová správa</a></li>
            </ul>
        </header>
        <section>
            <article id="form">
                <table>
                    <tr>
                        <td><span class="icon icon-user"></span>Prijímateľ:</td>
                        <td colspan="2"><input type="text" name="messageRecipient"  value="<?= (($this->recipient !== null) ? $this->recipient->getLogin() : ''); ?>" style="width: 200px;"></td>
                    </tr><tr>
                        <td><span class="icon icon-comments"></span>Text:</td>
                        <td><textarea name="messageText" style="width: 670px; height: 70px;"></textarea></td>
                    </tr><tr>
                        <td colspan="3"><div class="hr"></div></td>
                    </tr><tr>
                        <td colspan="3">
                            <div class="button negative" onclick="javascript: window.location = '<?= $this->returnAction; ?>';"><span class="icon icon-back"></span>Zrušiť</div>
                            <div class="button positive" onClick="javascript: $('#messageForm').submit();"><span class="icon icon-message"></span>Odoslať</div>
                        </td>
                    </tr>
                </table>
            </article>
        </section>
    </section>

</form>