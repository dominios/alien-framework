
<script type="text/javascript">
    $(document).ready(function() {
        $("#tabs").tabs();
    });
</script>

<?=$this->form->startTag();?>

<section class="tabs" id="userTabs">
    <header>
        <ul>
            <li class="active"><a href="#config"><span class="icon icon-service"></span>Konfigurácia</a></li>
        </ul>
    </header>
    <section>
        <article id="config" class="">
            <table class="full">
                <tr>
                    <td><span class="icon icon-file"></span>Názov widgetu:</td>
                    <td><?=$this->form->getElement('widgetName');?></td>
                </tr>
                <tr>
                    <td><span class="icon icon-screen"></span>Radio:</td>
                    <td><?=$this->form->getElement('widgetRadio');?></td>
                </tr>
                <tr>
                    <td><span class="icon icon-screen"></span>Checkbox:</td>
                    <td><?=$this->form->getElement('widgetCheckbox');?></td>
                </tr>
                <tr>
                    <td><span class="icon icon-screen"></span>Select:</td>
                    <td><?=$this->form->getElement('aaa');?></td>
                </tr>
            </table>
        </article>
    </section>
</section>
<?=$this->form->endTag();?>

<? return; ?>













    <input type="hidden" name="action" value="widgetFormSubmit">
    <input type="hidden" name="widgetId" value="<?= $this->widget->getId(); ?>">

    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Konfigurácia</a></li>
            <li><a href="#tabs-2">Skupiny</a></li>
            <li><a href="#tabs-3">Oprávnania</a></li>
        </ul>
        <div id="tabs-1">
            <table>
                <tr>
                    <td><img src="<?= Alien::$SystemImgUrl; ?>file_unknown.png" alt="name"> Objekt:</td>
                    <td colspan="2"><input type="text" name="widgetItem" value="<?= $this->widget->getItem(true)->getId(); ?>" style="width: 600px;"></td>
                </tr><tr>
                    <td><img src="<?= Alien::$SystemImgUrl; ?>file_unknown.png" alt="name"> Viditeľnosť:</td>
                    <td colspan="2"><input type="text" name="templateDesc" value="<?= (int) $this->widget->isVisible(); ?>" style="width: 600px;"></td>
                </tr><tr>
                    <td><img src="<?= Alien::$SystemImgUrl; ?>file_unknown.png" alt="php"> Zobrazovač:</td>
                    <td colspan="2"><input type="text" name="templatePhp" value="<?= $this->widget->getScript(); ?>" style="width: 505px;"></td>
                </tr><tr>
                    <td colspan="3"><hr></td>
                </tr><tr>
                    <td colspan="3">
                        <div class="button negative" onclick="javascript: window.location = '<?= $this->returnAction; ?>';"><img src="<?= Alien::$SystemImgUrl; ?>back.png" alt="cancel"> Zrušiť</div>
                        <div class="button positive" onclick="javascript: $('#widgetForm').submit();"><img src="<?= Alien::$SystemImgUrl; ?>save.png" alt="save"> Uložiť šablónu</div>
                    </td>
                </tr>
            </table>
        </div>
        <div id="tabs-2">
            <p>todo
            </p>
        </div>
    </div>


