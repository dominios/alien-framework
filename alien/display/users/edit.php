<script type="text/javascript">
    $(document).ready(function() {
        markBadInputs();
        $("input.invalidInput").mouseover(function() {
            $(this).next('div').fadeIn(400);
        });
        $("input.invalidInput").mouseout(function() {
            $(this).next('div').fadeOut(400);
        });
    });

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

<form name="editUserForm" method="POST" action="" id="userForm">
    <input type="hidden" name="action" value="users/userFormSubmit">
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
                <table>
                    <tr>
                        <td><span class="icon icon-user"></span>Login:</td>
                        <td colspan="2"><input type="text" name="userLogin" value="<?= $this->user->getLogin(); ?>" autocomplete="off" style="width: 600px;"></td>
                    </tr><tr>
                        <td><span class="icon icon-user"></span>Meno:</td>
                        <td colspan="2"><input type="text" name="userFirstname" value="<?= $this->user->getFirstname(); ?>" autocomplete="off" style="width: 600px;"></td>
                    </tr><tr>
                        <td><span class="icon icon-user"></span>Priezvisko:</td>
                        <td colspan="2"><input type="text" name="userSurname" value="<?= $this->user->getSurname(); ?>" autocomplete="off" style="width: 600px;"></td>
                    </tr><tr>
                        <td><span class="icon icon-email"></span>Email:</td>
                        <!--<td colspan="2"><input type="text" name="userEmail" value="<?= $this->user->getEmail(); ?>" autocomplete="off" style="width: 600px;"></td>-->
                        <td colspan="2"><?= $this->inputEmail; ?></td>
                    </tr><tr>
                        <td><span class="icon icon-key"></span>Nové heslo:</td>
                        <td colspan="2"><input type="password" name="userPass2" autocomplete="off" style="width: 600px;"></td>
                    </tr><tr>
                        <td><span class="icon icon-key"></span>Potvrdiť heslo:</td>
                        <td colspan="2"><input type="password" name="userPass3"autocomplete="off" style="width: 600px;"></td>
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
                            <div class="button negative" onclick="javascript: window.location = '<?= $this->returnAction; ?>';"><span class="icon icon-back"></span>Zrušiť</div>
                            <div class="button positive" onclick="javascript: $('#userForm').submit();"><span class="icon icon-save"></span>Uložiť</div>
                            <div class="button neutral" onclick="javascript: window.location = '<?= $this->sendMessageAction; ?>';"><span class="icon icon-message"></span>Napísať správu</div>
                            <div class="button neutral disabled" onclick="javascript: window.location = '<?= $this->resetPasswordAction; ?>';"><span class="icon icon-shield"></span>Resetovať heslo</div>
                            <div class="button negative disabled" onclick="javascript: window.location = '<?= $this->deleteUserAction; ?>';"><span class="icon icon-delete"></span>Odstrániť používateľa</div>
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
                <div class="button neutral" onClick="javascript: userShowAddGroupDialog(<?= $this->user->getId(); ?>);"><span class="icon icon-plus"></span>Pridať skupinu</div>
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
                <div class="button neutral" onClick="javascript: userShowAddPermissionDialog(<?= $this->user->getId(); ?>);"><span class="icon icon-plus"></span>Pridať oprávnenie</div>
            </article>
        </section>
    </section>
</form>