<section class="tabs" id="messageTabs">
    <header>
        <ul>
            <li class="active"><a href="#inbox"><span class="icon icon-message"></span>Prijatá pošta<?= (\Alien\Message::getUnreadCount(\Alien\Authorization\Authorization::getCurrentUser()) ? '<span class="badge badge-normal">' . \Alien\Message::getUnreadCount(\Alien\Authorization\Authorization::getCurrentUser()) . '</badge>' : ''); ?></a></li>
            <li><a href="#outbox"><span class="icon icon-message-out"></span>Odoslaná pošta</a></li>
        </ul>
    </header>
    <section>
        <article id="inbox">

            <? if ($this->message != null): ?>
                <section id="messageDetail">
                    <p>Odosielateľ: <b><?= $this->message->getAuthor()->getName(); ?></b> (<?= $this->message->getDateSent('d.m.Y H:i:s'); ?>)</p>
                    <p><?= $this->message->getMessage(); ?></p>
                    <a href="<?= str_replace('%ID%', $this->message->getAuthor()->getId(), $this->replyMessagePattern); ?>" class="button"><span class="icon icon-reply"></span>Odpovedať</a>
                    <a href="<?= str_replace('%ID%', $this->message->getId(), $this->deleteMessagePattern); ?>" class="button"><span class="icon icon-trash"></span>Vymazať</a>
                </section>
            <? endif;
            ?>

            <table class="itemList">
                <tr class="itemHeaderRow">
                    <th></th>
                    <th>Odosielateľ</th>
                    <th>Dátum</th>
                    <th>Náhľad</th>
                    <!--<th>Tagy</th>-->
                </tr>
                <? if (!sizeof($this->inBox)): ?>
                    <tr class="">
                        <td class="itemDesc" colspan="4">
                            <?= Alien\Notification::inline('Žiadne správy v tomto priečinku.', Alien\Notification::INFO); ?>
                        </td>
                    </tr>
                <? endif; ?>
                <? foreach ($this->inBox as $message): ?>
                    <tr class="itemRow" onClick="javascrit: window.location = '<?= str_replace('%ID%', $message->getId(), $this->goToMessagePattern); ?>';">
                        <td class="itemDesc">
                            <span class="icon icon-<?= $message->isSeen() ? 'email2' : 'message'; ?>"></span>
                            <?
                            if (!$message->isSeen()) {
                                echo '<span class="badge badge-success">NEW</span>';
                            }
                            ?>
                        </td>
                        <td class="itemDesc"><?= $message->getAuthor()->getName(); ?></td>
                        <td class="itemDesc"><?= $message->getDateSent('d.m.Y H:i:s'); ?></td>
                        <td class="itemDesc"><?= substr($message->getMessage(), 0, 40) . '...'; ?></td>
                        <!--<td class="itemDesc"></td>-->
                    </tr>
                <? endforeach; ?>
            </table>
            <div class="hr"></div>
            <a class="button" href="<?= $this->composeMessageAction; ?>"><span class="icon icon-message"></span>Nová správa</a>
        </article>
        <article id="outbox" class="tab-hidden">
            <table class="itemList">
                <tr class="itemHeaderRow">
                    <th></th>
                    <th>Prijímateľ</th>
                    <th>Dátum</th>
                    <th>Náhľad</th>
                    <!--<th>Tagy</th>-->
                </tr>
                <? if (!sizeof($this->outBox)): ?>
                    <tr class="">
                        <td class="itemDesc" colspan="4">
                            <?= Alien\Notification::inline('Žiadne správy v tomto priečinku.', Alien\Notification::INFO); ?>
                        </td>
                    </tr>
                <? endif; ?>
                <? foreach ($this->outBox as $message): ?>
                    <tr class="itemRow" onClick="javascrit: window.location = '<?= str_replace('%ID%', $message->getId(), $this->goToMessagePattern); ?>';">
                        <td class="itemDesc">
                            <span class="icon icon-<?= $message->isSeen() ? 'email2' : 'message'; ?>"></span>
                            <?
                            if (!$message->isSeen()) {
                                echo '<span class="badge badge-normal">UNSEEN</span>';
                            }
                            ?>
                        </td>
                        <td class="itemDesc"><?= $message->getRecipient()->getName(); ?></td>
                        <td class="itemDesc"><?= $message->getDateSent('d.m.Y H:i:s'); ?></td>
                        <td class="itemDesc"><?= substr($message->getMessage(), 0, 40) . '...'; ?></td>
                        <!--<td class="itemDesc"></td>-->
                    </tr>
                <? endforeach; ?>
            </table>
            <div class="hr"></div>
            <a class="button" href="<?= $this->composeMessageAction; ?>"><span class="icon icon-message"></span>Nová správa</a>
        </article>
    </section>
</section>