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

    function createDialog(header, content) {
        $("#dialog-modal").remove();
        newhtml = "<div id='dialog-modal' title='" + header + "'>";
        newhtml += "<div id='dialog-content'><p>" + content + "</p></div>";
        newhtml += "</div>";
        $("body").append(newhtml);
        $(function() {
            $("#dialog-modal").dialog({
                modal: true,
                width: 'auto',
                height: 'auto',
                show: {
                    effect: 'drop',
                    duration: 100
                },
                hide: {
                    effect: 'drop',
                    duration: 100
                }
            });
        });
    }

    function pageShowTemplatesBrowser() {
        $.ajax({
            async: true,
            url: "ajax.php",
            type: "GET",
            data: "action=pageShowTemplatesBrowser",
            timeout: 5000,
            success: function(data) {
                json = jQuery.parseJSON(data);
                createDialog(json.header, json.content);
            }
        });
    }

    function chooseTemplate(id, name) {
        if (!id || !name)
            return;
        $("input[name=pageTemplateHelper]").attr('value', name);
        $("input[name=pageTemplate]").attr('value', id);
        $("#dialog-modal").dialog('close');
    }

    function markBadInputs() {
        json = jQuery.parseJSON('<?= $_SESSION['formErrorOutput']; ?>');
        if (json == null)
            return;
        for (i = 0; i <= json.length; i++) {
            item = json.pop();
            $("input[name=" + item.inputName + "]").addClass('invalidInput');
            $("<div class=\"inputErrorHelper\">" + item.errorMsg + "</div>").insertAfter($("input[name=" + item.inputName + "]"));
        }
<? unset($_SESSION['formErrorOutput']); ?>
    }

</script>

<form name="editPageForm" method="POST" action="" id="pageForm">
    <input type="hidden" name="action" value="pageFormSubmit">
    <input type="hidden" name="pageId" value="<?= $this->Page->getId(); ?>">

    <div id="tabs" style="margin: 0px 10px; box-shadow: 0px 0px 10px #ccc;">
        <ul>
            <li><a href="#tabs-1">Konfigurácia</a></li>
            <li><a href="#tabs-2">Obsah</a></li>
        </ul>
        <div id="tabs-1">
            <table>
                <tr>
                    <td><img src="<?= Alien::$SystemImgUrl; ?>page.png" alt="name"> Názov stránky:</td>
                    <td colspan="2"><input type="text" name="pageName" value="<?= $this->Page->getName(); ?>" style="width: 600px;"></td>
                </tr><tr>
                    <td><img src="<?= Alien::$SystemImgUrl; ?>note.png" alt="name"> Popis:</td>
                    <td colspan="2"><input type="text" name="pageDescription" value="<?= $this->Page->getDescription(); ?>" style="width: 600px;"></td>
                </tr><tr>
                    <td><img src="<?= Alien::$SystemImgUrl; ?>template.png" alt="name"> Šablóna:</td>
                    <td><input type="text" disabled name="pageTemplateHelper" value="<?= $this->Page->getTemplate(true)->getName(); ?>" style="width: 505px;"></td>
                    <td>
                        <input type="hidden" name="pageTemplate" value="<?= $this->Page->getTemplate(); ?>">
                        <div class="button" onclick="javascript: pageShowTemplatesBrowser();"><img src="<?= Alien::$SystemImgUrl; ?>external_link.png"></div>
                        <a class="button" href="?content=editTemplate&id=<?= $this->Page->getTemplate(); ?>" target="_blank"><img src="<?= Alien::$SystemImgUrl; ?>magnifier.png"></a>
                    </td>
                </tr><tr>
                    <td><img src="<?= Alien::$SystemImgUrl; ?>link.png" alt="name"> Seolink:</td>
                    <td colspan="2"><input type="text" name="pageSeolink" value="<?= $this->Page->getSeolink(); ?>" style="width: 600px;"></td>
                </tr><tr>
                    <td><img src="<?= Alien::$SystemImgUrl; ?>tag.png" alt="name"> Kľúčové slová:</td>
                    <td colspan="2"><input type="text" name="pageKeywords" value="<?= $this->Page->getKeywords(); ?>" style="width: 600px;"></td>
                </tr><tr>
                    <td><img src="<?= Alien::$SystemImgUrl; ?>folder.png" alt="name"> Adresár:</td>
                    <td colspan="2"><input type="text" name="pageFolder" value="<?= $this->Page->getFolder(); ?>" style="width: 600px;"></td>
                </tr><tr>
                    <td colspan="3"><hr></td>
                </tr><tr>
                    <td colspan="3">
                        <div class="button negative" onclick="javascript: window.location = '<?= $this->ReturnAction; ?>';"><img src="<?= Alien::$SystemImgUrl; ?>back.png" alt="cancel"> Zrušiť</div>
                        <div class="button positive" onclick="javascript: $('#pageForm').submit();"><img src="<?= Alien::$SystemImgUrl; ?>save.png" alt="save"> Uložiť stránku</div>
                        <a class="button neutral" href="../<?= $this->Page->getSeolink(); ?>" target="_blanc"><img src="<?= Alien::$SystemImgUrl; ?>forward.png" alt="save"> Prejsť na stránku</a>
                    </td>
                </tr>
            </table>
        </div>
        <div id="tabs-2">
            <p>Work in progress...</p>
        </div>
    </div>


</form>