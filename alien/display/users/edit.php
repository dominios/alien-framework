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

<?= $this->unescaped(formStartTag); ?>
<?= $this->inputAction; ?>
<input type="hidden" name="userId" value="<?= $this->user->getId(); ?>">
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
                    <td colspan="2"><?= $this->inputLogin; ?></td>
                </tr><tr>
                    <td><span class="icon icon-user"></span>Meno:</td>
                    <td colspan="2"><?= $this->inputFirstname; ?></td>
                </tr><tr>
                    <td><span class="icon icon-user"></span>Priezvisko:</td>
                    <td colspan="2"><?= $this->inputSurname; ?></td>
                </tr><tr>
                    <td><span class="icon icon-email"></span>Email:</td>
                    <td colspan="2"><?= $this->inputEmail; ?></td>
                </tr><tr>
                    <td><span class="icon icon-key"></span>Nové heslo:</td>
                    <td colspan="2"><?= $this->inputPass2; ?></td>
                </tr><tr>
                    <td><span class="icon icon-key"></span>Potvrdiť heslo:</td>
                    <td colspan="2"><?= $this->inputPass3; ?></td>
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
                        <?= $this->buttonCancel; ?>
                        <?= $this->buttonSave; ?>
                        <?= $this->buttonMessage; ?>
                        <?= $this->buttonResetPassword; ?>
                        <?= $this->buttonDelete; ?>
                    </td>
                </tr>
            </table>
        </article>
        <article id="groups" style="display: none;">
            <div class="gridLayout">
                <?
                foreach ($this->userGroups as $group):
                    $partialView = new \Alien\View('display/common/item.php');
                    $partialView->icon = 'group';
                    $partialView->item = $group;
                    $partialView->dropLink = \Alien\Controllers\BaseController::actionURL('users', 'removeGroup', array('user' => $this->user->getId(), 'group' => $group->getId()));
                    echo $partialView->renderToString();
                endforeach;
                ?>
            </div>
            <div class="cleaner"></div>
            <div class="hr"></div>
            <?= $this->buttonAddGroup; ?>
        </article>
        <article id="permissions" style="display: none;">
            <div class="gridLayout">
                <?
                foreach ($this->userPermissions as $permission):
                    $partialView = new \Alien\View('display/common/item.php');
                    $partialView->icon = 'shield';
                    $partialView->item = $permission;
                    $partialView->dropLink = \Alien\Controllers\BaseController::actionURL('users', 'removePermission', array('user' => $this->user->getId(), 'permission' => $permission->getId()));
                    echo $partialView->renderToString();
                endforeach;
                ?>
            </div>
            <div class="cleaner"></div>
            <div class="hr"></div>
            <?= $this->buttonAddPermission; ?>
        </article>
    </section>
</section>
<?= $this->unescaped(formEndTag); ?>