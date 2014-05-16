<?= $this->form->startTag(); ?>
<?= $this->form->getField('action'); ?>
<?= $this->form->getField('userId'); ?>
<section class="tabs" id="userTabs">
    <header>
        <ul>
            <li class="active"><a href="#user"><span class="icon icon-user"></span>Používateľ</a></li>
            <li><a href="#groups"><span class="icon icon-group"></span>Skupiny</a></li>
            <li><a href="#permissions"><span class="icon icon-shield"></span>Oprávnenia</a></li>
        </ul>
    </header>
    <section>
        <article id="user">
            <table class="full">
                <tr>
                    <td style="width: 180px;"><span class="icon icon-user"></span>Login:</td>
                    <td colspan="2"><?= $this->form->getField('userLogin'); ?></td>
                </tr>
                <tr>
                    <td><span class="icon icon-user"></span>Meno:</td>
                    <td colspan="2"><?= $this->form->getField('userFirstname'); ?></td>
                </tr>
                <tr>
                    <td><span class="icon icon-user"></span>Priezvisko:</td>
                    <td colspan="2"><?= $this->form->getField('userSurname'); ?></td>
                </tr>
                <tr>
                    <td><span class="icon icon-email"></span>Email:</td>
                    <td colspan="2"><?= $this->form->getField('userEmail'); ?></td>
                </tr>
                <tr>
                    <td><span class="icon icon-key"></span>Súčasné heslo:</td>
                    <td colspan="2"><?= $this->form->getField('userCurrentPass'); ?></td>
                </tr>
                <tr>
                    <td><span class="icon icon-key"></span>Nové heslo:</td>
                    <td colspan="2"><?= $this->form->getField('userPass2'); ?></td>
                </tr>
                <tr>
                    <td><span class="icon icon-key"></span>Potvrdiť heslo:</td>
                    <td colspan="2"><?= $this->form->getField('userPass3'); ?></td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div class="hr"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <?= $this->form->getField('buttonCancel'); ?>
                        <?= $this->form->getField('buttonSave'); ?>
                    </td>
                </tr>
            </table>
        </article>
        <article id="groups" class="tab-hidden">
            <div class="gridLayout">
                <?
                foreach ($this->userGroups as $group):
                    $partialView = new \Alien\View('display/common/item.php');
                    $partialView->icon = 'group';
                    $partialView->item = $group;
                    echo $partialView->renderToString();
                endforeach;
                ?>
            </div>
            <div class="cleaner"></div>
        </article>
        <article id="permissions" class="tab-hidden">
            <div class="gridLayout">
                <?
                foreach ($this->userPermissions as $permission):
                    $partialView = new \Alien\View('display/common/item.php');
                    $partialView->icon = 'shield';
                    $partialView->item = $permission;
                    echo $partialView->renderToString();
                endforeach;
                ?>
            </div>
            <div class="cleaner"></div>
        </article>
    </section>
</section>
<?= $this->form->endTag(); ?>