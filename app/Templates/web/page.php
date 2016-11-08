<?php
if (!empty($page)) {
?>

<nav>
    <ul class="pagination text-right">
    <li>
        <a href="<?=$page->getLinkString()?>cp=<?=$page->getFrontPage()?>" aria-label="Previous">
            <span aria-hidden="true">&laquo;</span>
        </a>
    </li>
    <?php
        for ($i = $page->getPageStepBegin(); $i <= $page->getPageStepEnd(); $i++) {
    ?>
        <?php
            if ($i == $page->getCurrentPage()) {
        ?>
            <li class="active"><a href="#"><?=$i?></a></li>
        <?php } else { ?>
            <li><a href="<?=$page->getLinkString()?>cp=<?=$i?>"><?=$i?></a></li>
        <?php } ?>
    <?php } ?>
    <li>
        <a href="<?=$page->getLinkString()?>cp=<?=$page->getNextPage()?>" aria-label="Next">
            <span aria-hidden="true">&raquo;</span>
        </a>
    </li>
    </ul>
</nav>
<?php } ?>