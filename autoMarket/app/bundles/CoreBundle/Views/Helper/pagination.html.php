<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$target = (!empty($target)) ? $target : '.page-list';
$tmpl   = (!empty($tmpl)) ? $tmpl : 'list';

$limit  = (!isset($limit)) ? 30 : (int) $limit;

if (!isset($range)) {
    $range = 4;
}

if ($page <= 0) {
    $page = 1;
} else {
    $page = (int) $page;
}

$totalPages = ($limit) ? (int) ceil($totalItems / $limit) : 1;

$pageClass = (!isset($paginationClass)) ? "" : " pagination-$paginationClass";
$menuLink  = (!empty($menuLinkId)) ? " data-menu-link=\"$menuLinkId\"" : "";
$paginationWrapper = isset($paginationWrapper) ? $paginationWrapper :  "pagination-wrapper pull-left ml-md mr-md";

$queryString = '?tmpl=' . $tmpl . (isset($queryString) ? $queryString : '');

$limitOptions = array(
    5   => '5',
    10  => '10',
    15  => '15',
    20  => '20',
    25  => '25',
    30  => '30',
    50  => '50',
    100 => '100'
);

$formExit = (!empty($ignoreFormExit)) ? ' data-ignore-formexit="true"' : '';

$responsiveViewports = array('desktop', 'mobile');

foreach ($responsiveViewports as $viewport):

if ($viewport == 'mobile'):
    $paginationClass   = 'sm';
    $pageClass         = 'pagination-sm';
    $responsiveClass   = 'visible-xs hidden-sm hidden-md hidden-lg';
    $paginationWrapper = "pagination-wrapper pull-left nm";
else:
    $responsiveClass = 'hidden-xs visible-sm visible-md visible-lg';
endif;

?>
<div class="<?php echo $responsiveClass; ?>">
    <?php if (empty($fixedLimit)): ?>
        <div class="pull-right">
            <?php $class = (!empty($paginationClass)) ? " input-{$paginationClass}" : ""; ?>
            <select autocomplete="false" class="form-control not-chosen pagination-limit<?php echo $class; ?>" onchange="Mautic.limitTableData('<?php echo $sessionVar; ?>',this.value,'<?php echo $tmpl; ?>','<?php echo $target; ?>'<?php if (!empty($baseUrl)): ?>, '<?php echo $baseUrl; ?>'<?php endif; ?>);">
                <?php foreach ($limitOptions as $value => $label): ?>
                <?php $selected = ($limit === $value) ? ' selected="selected"': '';?>
                <option<?php echo $selected; ?> value="<?php echo $value; ?>"><?php echo $view['translator']->trans('mautic.core.pagination.'.$label); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endif; ?>

    <div class="<?php echo $paginationWrapper; ?>">
        <ul class="pagination nm <?php echo $pageClass; ?>">
            <?php
            $urlPage = "/" . ($page - $range);
            $url     = (($page - $range) > 0) ? $baseUrl . $urlPage . $queryString : 'javascript: void(0);';
            $data    = ($url == 'javascript: void(0);') ? '' : ' data-toggle="ajax" data-target="' . $target . '"' . $menuLink;
            $class   = (($page - $range) <= 0) ? ' class="disabled"' : '';
            ?>
            <li<?php echo $class; ?>>
                <?php  ?>
                <a href="<?php echo $url; ?>"<?php echo $data.$formExit; ?>>
                    <i class="fa fa-angle-double-left"></i>
                </a>
            </li>

            <?php
            $urlPage = "/" . ($page - 1);
            $url     = (($page - 1) >= 1) ? $baseUrl . $urlPage . $queryString : 'javascript: void(0);';
            $data    = ($url == 'javascript: void(0);') ? '' : ' data-toggle="ajax" data-target="' . $target . '"' . $menuLink;
            $class   = (($page - 1) <= 0) ? ' class="disabled"' : '';
            ?>
            <li<?php echo $class; ?>>
                <?php  ?>
                <a href="<?php echo $url; ?>"<?php echo $data.$formExit; ?>>
                    <i class="fa fa-angle-left"></i>
                </a>
            </li>

            <?php
            $startPage = $page - $range + 1;
            if ($startPage <= 0) {
                $startPage = 1;
            }
            $lastPage = $startPage + $range - 1;
            if ($lastPage > $totalPages) {
                $lastPage = $totalPages;
            }
            ?>
            <?php for ($i=$startPage; $i<=$lastPage; $i++): ?>
            <?php
            $class = ($page === (int) $i) ? ' class="active"' : '';
            $url   = ($page === (int) $i) ? 'javascript: void(0);' : $baseUrl . "/" . $i . $queryString;
            $data  = ($url == 'javascript: void(0);') ? '' : ' data-toggle="ajax" data-target="' . $target . '"' . $menuLink;
            ?>
            <li<?php echo $class; ?>>
                <a href="<?php echo $url; ?>"<?php echo $data.$formExit; ?>>
                    <span><?php echo $i; ?></span>
                </a>
            </li>
            <?php endfor; ?>

            <?php
            $urlPage = "/" . ($page + 1);
            $url     = (($page + 1) <= $totalPages) ? $baseUrl . $urlPage . $queryString : 'javascript: void(0);';
            $data    = ($url == 'javascript: void(0);') ? '' : 'data-toggle="ajax" data-target="' . $target . '"' . $menuLink;
            $class   = (($page + 1) > $totalPages) ? ' class="disabled"' : '';
            ?>
            <li<?php echo $class; ?>>
                <?php  ?>
                <a href="<?php echo $url; ?>" <?php echo $data.$formExit; ?>>
                    <i class="fa fa-angle-right"></i>
                </a>
            </li>

            <?php
            $urlPage = "/" . ($page + $range);
            if (($page + $range) > $totalPages)
                $urlPage = "/" . $totalPages;
            $url     = ($page < $totalPages && $totalPages > $range) ? $baseUrl . $urlPage . $queryString : 'javascript: void(0);';
            $data    = ($url == 'javascript: void(0);') ? '' : ' data-toggle="ajax" data-target="' . $target . '"' . $menuLink;
            $class   = (($page + $range) == $totalPages || $page === $totalPages) ? ' class="disabled"' : '';
            ?>
            <li<?php echo $class; ?>>
                <?php  ?>
                <a href="<?php echo $url; ?>"<?php echo $data.$formExit; ?>>
                    <i class="fa fa-angle-double-right"></i>
                </a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
</div>
<?php endforeach; ?>
