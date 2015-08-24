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
            success: function (data) {
                json = jQuery.parseJSON(data);
//                createDialog(json.header, json.content);
                createModal(json);
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
            success: function (data) {
                json = jQuery.parseJSON(data);
//                createDialog(json.header, json.content);
                createModal(json);
            }
        });
    }
</script>

<div class="row">
    <div class="col-xs-12">
        <h1 id="forms" class="page-header"><?= $this->user->getName(); ?>
            <small><i class="fa fa-angle-double-right"></i> Úprava používateľa</small>
        </h1>
    </div>
</div>

<div class="row">

    <div class="col-xs-12">

        <?= $this->form->startTag(); ?>
        <?= $this->form->getField('action'); ?>
        <?= $this->form->getField('userId'); ?>

        <ul class="nav nav-tabs" role="tablist" id="myTab">
            <li role="presentation" class="active">
                <a href="#profile" role="tab" data-toggle="tab"><i class="fa fa-user hidden-xs"></i> Profil</a>
            </li>
            <li role="presentation">
                <a href="#groups" role="tab" data-toggle="tab"><i class="fa fa-group hidden-xs"></i> Skupiny</a>
            </li>
            <li role="presentation">
                <a href="#permissions" role="tab" data-toggle="tab"><i class="fa fa-key hidden-xs"></i> Oprávnenia</a>
            </li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="profile">

                <div class="form-group">
                    <label for="userLogin" class="col-sm-2 control-label">Login</label>

                    <div class="col-sm-10">
                        <?= $this->form->getField('userLogin'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="userFirstname" class="col-sm-2 control-label">Meno</label>

                    <div class="col-sm-10">
                        <?= $this->form->getField('userFirstname'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="userSurname" class="col-sm-2 control-label">Priezvisko</label>

                    <div class="col-sm-10">
                        <?= $this->form->getField('userSurname'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="userEmail" class="col-sm-2 control-label">Email</label>

                    <div class="col-sm-10">
                        <?= $this->form->getField('userEmail'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="userPass2" class="col-sm-2 control-label">Nové heslo</label>

                    <div class="col-sm-10">
                        <?= $this->form->getField('userPass2'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="userPass3" class="col-sm-2 control-label">Potrvdiť</label>

                    <div class="col-sm-10">
                        <?= $this->form->getField('userPass3'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="userPass3" class="col-sm-2 control-label">Status</label>

                    <div class="col-sm-10">
                        <?= $this->form->getField('userStatus'); ?>
                    </div>
                </div>

                <div class="hr"></div>

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <?= $this->form->getField('buttonCancel'); ?>
                        <?= $this->form->getField('buttonSave'); ?>
                        <?= $this->form->getField('buttonMessage'); ?>
                        <?= $this->form->getField('buttonResetPassword'); ?>
                        <?= $this->form->getField('buttonDelete'); ?>
                    </div>
                </div>
            </div>


            <div role="tabpanel" class="tab-pane" id="groups">
                <table class="table table-condensed">
                    <?
                    $user = $this->user;
                    foreach ($this->groups as $group) {
                        if ($group instanceof \Alien\Models\Authorization\Group && $user instanceof \Alien\Models\Authorization\User) {
                            echo '<tr>';
                            if ($user->isMemberOfGroup($group)) {
                                echo '<td><i class="fa fa-fw fa-check text-success"></i> členom</td>';
                                echo '<td><i class="fa fa-fw fa-group"></i> ' . $group->getName() . '</td>';
                                echo '<td><a href="' . \Alien\Router::getRouteUrl('user/removeGroup/' . $user->getId() . '-' . $group->getId()) . '" class="text-danger"><i class="fa fa-fw fa-trash-o"></i></a></td>';
                            } else {
                                echo '<td><i class="fa fa-fw fa-times text-danger"></i> nie je členom</td>';
                                echo '<td><i class="fa fa-fw fa-group"></i> ' . $group->getName() . '</td>';
                                echo '<td><a href="' . \Alien\Router::getRouteUrl('user/addGroup/' . $user->getId() . '-' . $group->getId()) . '" class="text-primary"><i class="fa fa-fw fa-plus-circle"></i></a></td>';
                            }
                            echo "</tr>";
                        }
                    }
                    ?>
                </table>
                <?
                //                foreach ($this->userGroups as $group):
                //                    $partialView = new \Alien\View('display/common/item.php');
                //                    $partialView->icon = 'group';
                //                    $partialView->item = $group;
                //                    $partialView->dropLink = \Alien\Controllers\ AbstractController:: staticActionURL('users', 'removeGroup', array('user' => $this->user->getId(), 'group' => $group->getId()));
                //                    echo $partialView->renderToString();
                //                endforeach;
                ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="permissions">
                <?
                foreach ($this->userPermissions as $permission):
                    $partialView = new \Alien\View('display/common/item.php');
                    $partialView->icon = 'shield';
                    $partialView->item = $permission;
                    $partialView->dropLink = \Alien\Controllers\ AbstractController::staticActionURL('users', 'removePermission', array('user' => $this->user->getId(), 'permission' => $permission->getId()));
                    echo $partialView->renderToString();
                endforeach;
                ?>
            </div>
        </div>

        <?= $this->form->endTag(); ?>


    </div>
</div>

<? return; ?>

<section class="tabs" id="userTabs">
    <header>
        <ul>
            <li class="active"><a href="#user"><span class="icon icon-user"></span>Používateľ</a></li>
            <li><a href="#groups"><span class="icon icon-group"></span>Skupiny</a></li>
            <li><a href="#permissions"><span class="icon icon-shield"></span>Oprávnenia</a></li>
        </ul>
    </header>
    <section>

        <article id="groups" class="tab-hidden">
            <div class="gridLayout">
                <?
                foreach ($this->userGroups as $group):
                    $partialView = new \Alien\View('display/common/item.php');
                    $partialView->icon = 'group';
                    $partialView->item = $group;
                    $partialView->dropLink = \Alien\Controllers\ AbstractController:: staticActionURL('users', 'removeGroup', array('user' => $this->user->getId(), 'group' => $group->getId()));
                    echo $partialView->renderToString();
                endforeach;
                ?>
            </div>
            <div class="cleaner"></div>
            <div class="hr"></div>
            <?= $this->form->getField('buttonAddGroup'); ?>
        </article>
        <article id="permissions" class="tab-hidden">
            <div class="gridLayout">
                <?
                foreach ($this->userPermissions as $permission):
                    $partialView = new \Alien\View('display/common/item.php');
                    $partialView->icon = 'shield';
                    $partialView->item = $permission;
                    $partialView->dropLink = \Alien\Controllers\ AbstractController::staticActionURL('users', 'removePermission', array('user' => $this->user->getId(), 'permission' => $permission->getId()));
                    echo $partialView->renderToString();
                endforeach;
                ?>
            </div>
            <div class="cleaner"></div>
            <div class="hr"></div>
            <?= $this->form->getField('buttonAddPermission'); ?>
        </article>
    </section>
</section>
<?= $this->form->endTag(); ?>