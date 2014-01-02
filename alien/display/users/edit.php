<script type="text/javascript">
    function userShowAddGroupDialog(userId) {
        if (!userId) {
            return;
        }
        $.ajax({
            async: true,
            url: "/alien/ajax.php",
            type: "GET",
            data: "action=userShowAddGroupDialog&userId=" + userId,
            timeout: 5000,
            success: function(data) {
                json = jQuery.parseJSON(data);
                createDialog(json.header, json.content);
            }
        });
    }
    function userShowAddPermissionDialog(userId) {
        if (!userId) {
            return;
        }
        $.ajax({
            async: true,
            url: "/alien/ajax.php",
            type: "GET",
            data: "action=userShowAddPermissionDialog&userId=" + userId,
            timeout: 5000,
            success: function(data) {
                json = jQuery.parseJSON(data);
                createDialog(json.header, json.content);
            }
        });
    }
</script>

<?= $this->form->startTag(); ?>
<?= $this->form->getElement('action'); ?>
<?= $this->form->getElement('userId'); ?>
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
                    <td colspan="2"><?= $this->form->getElement('userLogin'); ?></td>
                </tr><tr>
                    <td><span class="icon icon-user"></span>Meno:</td>
                    <td colspan="2"><?= $this->form->getElement('userFirstname'); ?></td>
                </tr><tr>
                    <td><span class="icon icon-user"></span>Priezvisko:</td>
                    <td colspan="2"><?= $this->form->getElement('userSurname'); ?></td>
                </tr><tr>
                    <td><span class="icon icon-email"></span>Email:</td>
                    <td colspan="2"><?= $this->form->getElement('userEmail'); ?></td>
                </tr><tr>
                    <td><span class="icon icon-key"></span>Nové heslo:</td>
                    <td colspan="2"><?= $this->form->getElement('userPass2'); ?></td>
                </tr><tr>
                    <td><span class="icon icon-key"></span>Potvrdiť heslo:</td>
                    <td colspan="2"><?= $this->form->getElement('userPass3'); ?></td>
                </tr><tr>
                    <td><span class="icon icon-checked-user"></span>Stav účtu:</td>
                    <td colspan="2">
                        <select name="userStatus">
                            <option value="0" <?= !$this->user->getStatus() ? 'selected' : ''; ?>>Neaktívny</option>
                            <option value="1" <?= $this->user->getStatus() ? 'selected' : ''; ?>>Aktívny</option>
                        </select>
                    </td>
                </tr><tr>
                    <td colspan="3"><div class="hr"></div></td>
                </tr><tr>
                    <td colspan="3">
                        <?= $this->form->getElement('buttonCancel'); ?>
                        <?= $this->form->getElement('buttonSave'); ?>
                        <?= $this->form->getElement('buttonMessage'); ?>
                        <?= $this->form->getElement('buttonResetPassword'); ?>
                        <?= $this->form->getElement('buttonDelete'); ?>
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
                    $partialView->dropLink = \Alien\Controllers\ BaseController:: actionURL('users', 'removeGroup', array('user' => $this->user->getId(), 'group' => $group->getId()));
                    echo $partialView->renderToString();
                endforeach;
                ?>
            </div>
            <div class="cleaner"></div>
            <div class="hr"></div>
            <?= $this->form->getElement('buttonAddGroup'); ?>
        </article>
        <article id="permissions" class="tab-hidden">
            <div class="gridLayout">
                <?
                foreach ($this->userPermissions as $permission):
                    $partialView = new \Alien\View('display/common/item.php');
                    $partialView->icon = 'shield';
                    $partialView->item = $permission;
                    $partialView->dropLink = \Alien\Controllers\ BaseController::actionURL('users', 'removePermission', array('user' => $this->user->getId(), 'permission' => $permission->getId()));
                    echo $partialView->renderToString();
                endforeach;
                ?>
            </div>
            <div class="cleaner"></div>
            <div class="hr"></div>
            <?= $this->form->getElement('buttonAddPermission'); ?>
        </article>
    </section>
</section>
<?= $this->form->endTag(); ?>