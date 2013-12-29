<?

namespace Alien\Models\Content\Template;

use Alien\Models\Content\TemplateBlock;
?>

<script type="text/javascript">
    $(function() {
        $(".toggled-content").hide();
        $("#<?= $_SESSION['temp_toolbox']; ?>").show();
        $(".toggler").click(function(ev) {
            id = $(this).attr('data-toggle');
            $("#" + id).slideToggle(300, 'easeInOutBack');
            $(".toggled-content:not(#" + id + ")").slideUp(300, 'easeInOutBack');
            setSession('toolbox', id);
            ev.preventDefault();
        });
    });
</script>

<?
echo '<h1><span class="icon icon-settings"></span>Panel nástrojov</h1>';
echo '<div class="hr"></div>';

echo '<h2>Pridať box<span class="toggler icon icon-xs icon-menu" style="float: right;" data-toggle="boxmenu"></span></h2>';
echo '<div class="toggled-content" id="boxmenu">';
$blocks = TemplateBlock::getList(true);
foreach ($blocks as $block):
    echo '<a href="#"><span class="icon icon-puzzle"></span>' . $block->getName() . '</a>';
endforeach;
echo '<a href="#"><span class="icon icon-add"></span>nový</a>';
echo '</div>';

echo '<h2>Pridať widget<span class="toggler icon icon-xs icon-menu" style="float: right;" data-toggle="widgetmenu"></span></h2>';
echo '<div class="toggled-content" id="widgetmenu">';
echo '<a href="#"><span class="icon icon-variable"></span>variabilná oblasť</a>';
echo '<a href="#"><span class="icon icon-box"></span>balíček</a>';
echo '<a href="#"><span class="icon icon-php2"></span>php</a>';
echo '<a href="#" class="item-creatable" data-type="CodeItemWidget"><span class="icon icon-code"></span>html</a>';
echo '<a href="#"><span class="icon icon-document"></span>text</a>';
echo '<a href="#"><span class="icon icon-magazine"></span>novinky</a>';
echo '<a href="#"><span class="icon icon-gallery"></span>galéria</a>';
echo '<a href="#"><span class="icon icon-book-stack"></span>dokumenty</a>';
echo '<a href="#"><span class="icon icon-list"></span>menu</a>';
echo '<a href="#"><span class="icon icon-console"></span>formulár</a>';
echo '<a href="#"><span class="icon icon-menu"></span>ďalšie</a>';
echo '</div>';

echo '<h2>Triedenie<span class="toggler icon icon-xs icon-menu" style="float: right;" data-toggle="sortmenu"></span></h2>';
echo '<div class="toggled-content" id="sortmenu">';
echo '<a href="#"><span class="icon icon-move"></span>preusporiadať</a>';
echo '<a href="#"><span class="icon icon-save"></span>uložiť zmeny</a>';
echo '<a href="#"><span class="icon icon-trash"></span>zahodiť zmeny</a>';
echo '</div>';
