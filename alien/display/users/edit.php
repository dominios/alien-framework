<?

namespace Alien;
?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#tabs").tabs();
        markBadInputs();
        $("input.invalidInput").mouseover(function() {
            $(this).next('div').fadeIn(400);
        });
        $("input.invalidInput").mouseout(function() {
            $(this).next('div').fadeOut(400);
        });
    });
</script>

<form name="editPageForm" method="POST" action="" id="userForm">
    <input type="hidden" name="action" value="userFormSubmit">
    <input type="hidden" name="userId" value="<?= $this->User->getId(); ?>">

    <div id="tabs" style="margin: 0px 10px; box-shadow: 2px 2px 10px #888;">
        <ul>
            <li><a href="#tabs-1">Používateľ</a></li>
            <li><a href="#tabs-2">Skupiny</a></li>
            <li><a href="#tabs-3">Oprávnenia</a></li>
        </ul>
        <div id="tabs-1">
            <table>
                <tr>
                    <td><img src="<?= Alien::$SystemImgUrl; ?>user.png" alt="login"> Login:</td>
                    <td colspan="2"><input type="text" name="userLogin" value="<?= $this->User->getLogin(); ?>" style="width: 600px;"></td>
                </tr><tr>
                    <td><img src="<?= Alien::$SystemImgUrl; ?>user.png" alt="meno"> Meno:</td>
                    <td colspan="2"><input type="text" name="userFirstname" value="<?= $this->User->getFirstname(); ?>" style="width: 600px;"></td>
                </tr><tr>
                    <td><img src="<?= Alien::$SystemImgUrl; ?>user.png" alt="name"> Priezvisko:</td>
                    <td colspan="2"><input type="text" name="userSurname" value="<?= $this->User->getSurname(); ?>" style="width: 600px;"></td>
                </tr><tr>
                    <td><img src="<?= Alien::$SystemImgUrl; ?>post.png" alt="email"> Email:</td>
                    <td colspan="2"><input type="text" name="userEmail" value="<?= $this->User->getEmail(); ?>" style="width: 600px;"></td>
                </tr><tr>
                    <td><img src="<?= Alien::$SystemImgUrl; ?>key.png" alt="nové heslo"> Nové heslo:</td>
                    <td colspan="2"><input type="password" name="userPass2" autocomplete="0" style="width: 600px;"></td>
                </tr><tr>
                    <td><img src="<?= Alien::$SystemImgUrl; ?>key.png" alt="potvrdiť nové heslo"> Potvrdiť heslo:</td>
                    <td colspan="2"><input type="password" name="userPass3" autocomplete="0" style="width: 600px;"></td>
                </tr><tr>
                    <td><img src="<?= Alien::$SystemImgUrl; ?>checked_user.png" alt="status"> Stav účtu:</td>
                    <td colspan="2">
<? /* <input type="text" name="userStatus" value="<?=(int)$this->User->getStatus();?>" style="width: 600px;"></td> */ ?>
                        <select name="userStatus">
                            <option value="0" <?= !$this->User->getStatus() ? 'selected' : ''; ?>>Neaktívny</option>
                            <option value="1" <?= $this->User->getStatus() ? 'selected' : ''; ?>>Aktívny</option>
                        </select>
                    </td>
                </tr><tr>
                    <td colspan="3"><hr></td>
                </tr><tr>
                    <td colspan="3">
                        <div class="button negative" onclick="javascript: window.location = '<?= $this->ReturnAction; ?>';"><img src="<?= Alien::$SystemImgUrl; ?>back.png" alt="cancel"> Zrušiť</div>
                        <div class="button positive" onclick="javascript: $('#pageForm').submit();"><img src="<?= Alien::$SystemImgUrl; ?>save.png" alt="save"> Uložiť</div>
                    </td>
                </tr>
            </table>
        </div>
        <div id="tabs-2">
            <p>Work in progress...</p>
        </div>
        <div id="tabs-3">
            <p>Work in progress...</p>
        </div>
    </div>


</form>