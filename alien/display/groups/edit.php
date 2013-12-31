<script type="text/javascript">
    function groupShowAddMemberDialog(groupId) {
        if (!groupId) {
            return;
        }
        $.ajax({
            async: true,
            url: "/alien/ajax.php",
            type: "GET",
            data: "action=groupShowAddMemberDialog&groupId=" + groupId,
            timeout: 5000,
            success: function(data) {
                json = jQuery.parseJSON(data);
                createDialog(json.header, json.content);
            }
        });
    }

    function groupShowAddPermissionDialog(groupId) {
        if (!groupId) {
            return;
        }
        $.ajax({
            async: true,
            url: "/alien/ajax.php",
            type: "GET",
            data: "action=groupShowAddPermissionDialog&groupId=" + groupId,
            timeout: 5000,
            success: function(data) {
                json = jQuery.parseJSON(data);
                createDialog(json.header, json.content);
            }
        });
    }
</script>

<?
$members = $this->group->getMembers(true);
$permissions = $this->group->getPermissions(true);
?>

<form name="editGroupForm" method="POST" action="" id="groupForm">
    <input type="hidden" name="action" value="groups/groupFormSubmit">
    <input type="hidden" name="groupId" value="<?= $this->group->getId(); ?>">
    <section class="tabs" id="groupTabs">
        <header>
            <ul>
                <li class="active"><a href="#group"><span class="icon icon-group"></span>Skupina</a></li>
                <li><a href="#members"><span class="icon icon-user"></span>Členovia</a></li>
                <li><a href="#permissions"><span class="icon icon-shield"></span>Oprávnenia</a></li>
            </ul>
        </header>
        <section>
            <article id="group">
                <table>
                    <tr>
                        <td><span class="icon icon-group"></span>Názov:</td>
                        <td colspan="2"><input type="text" name="groupName" value="<?= $this->group->getName(); ?>" autocomplete="off" style="width: 600px;"></td>
                    </tr><tr>
                        <td><span class="icon icon-note"></span>Popis:</td>
                        <td colspan="2"><input type="text" name="groupDescription" value="<?= $this->group->getDescription(); ?>" autocomplete="off" style="width: 600px;"></td>
                    </tr><tr>
                        <td colspan="3"><div class="hr"></div></td>
                    </tr><tr>
                        <td colspan="3">
                            <div class="button negative" onclick="javascript: window.location = '<?= $this->returnAction; ?>';"><span class="icon icon-cancel-light"></span>Zrušiť</div>
                            <div class="button positive" onclick="javascript: $('#groupForm').submit();"><span class="icon icon-tick-light"></span>Uložiť</div>
                            <div class="button negative <?= ($this->group->isDeletable() ? '' : 'disabled') ?>" onclick="javascript: window.location = '<?= $this->deleteAction; ?>';"><span class="icon icon-delete"></span>Odstrániť skupinu</div>
                        </td>
                    </tr>
                </table>
            </article>
            <article id="members" class="tab-hidden">
                <div class="gridLayout">
                    <?
                    foreach ($members as $member):
                        $partialView = new \Alien\View('display/common/item.php');
                        $partialView->icon = 'user';
                        $partialView->item = $member;
                        $partialView->dropLink = \Alien\Controllers\BaseController::actionURL('groups', 'removeMember', array('group' => $this->group->getId(), 'user' => $member->getId()));
                        echo $partialView->renderToString();
                    endforeach;
                    ?>
                </div>
                <div class="cleaner"></div>
                <div class="hr"></div>
                <div class="button neutral" onClick="javascript: groupShowAddMemberDialog(<?= $this->group->getId(); ?>);"><span class="icon icon-plus"></span>Pridať člena</div>
            </article>
            <article id="permissions" style="display: none;">
                <div class="gridLayout">
                    <?
                    foreach ($permissions as $permission):
                        $partialView = new \Alien\View('display/common/item.php');
                        $partialView->icon = 'shield';
                        $partialView->item = $permission;
                        $partialView->dropLink = \Alien\Controllers\BaseController::actionURL('groups', 'removePermission', array('group' => $this->group->getId(), 'permission' => $permission->getId()));
                        echo $partialView->renderToString();
                    endforeach;
                    ?>
                </div>
                <div class="cleaner"></div>
                <div class="hr"></div>
                <div class="button neutral" onClick="javascript: groupShowAddPermissionDialog(<?= $this->group->getId(); ?>);"><span class="icon icon-plus"></span>Pridať oprávnenie</div>
            </article>
        </section>
    </section>
</form>