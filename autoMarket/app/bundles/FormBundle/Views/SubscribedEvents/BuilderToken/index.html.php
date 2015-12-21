<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$searchBtnClass = (!empty($searchValue)) ? "fa-eraser" : "fa-search";
?>
<div class="input-group ma-5">
    <input type="search" class="form-control" id="formPageTokenSearch" name="search" placeholder="<?php echo $view['translator']->trans('mautic.core.search.placeholder'); ?>" value="<?php echo $searchValue; ?>" autocomplete="false" data-toggle="livesearch" data-target="#formPageTokens" data-action="<?php echo $view['router']->generate('mautic_form_pagetoken_index', array('page' => $page)); ?>" />
    <div class="input-group-btn">
        <button type="button" class="btn btn-default btn-search btn-filter" data-livesearch-parent="formPageTokenSearch">
            <i class="fa <?php echo $searchBtnClass; ?> fa-fw"></i>
        </button>
    </div>
</div>
<?php $view['slots']->output('_content'); ?>