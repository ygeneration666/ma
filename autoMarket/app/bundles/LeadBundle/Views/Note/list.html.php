<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

if ($tmpl == 'index') {
    $view->extend('MauticLeadBundle:Note:index.html.php');
}
?>

<ul class="notes" id="LeadNotes">
    <?php foreach ($notes as $note): ?>
        <?php
        //Use a separate layout for AJAX generated content
        echo $view->render('MauticLeadBundle:Note:note.html.php', array(
            'note'        => $note,
            'lead'        => $lead,
            'permissions' => $permissions
        )); ?>
    <?php endforeach; ?>
</ul>
<div class="notes-pagination">
    <?php echo $view->render('MauticCoreBundle:Helper:pagination.html.php', array(
        'totalItems'      => count($notes),
        'target'          => '#notes-container',
        'page'            => $page,
        'limit'           => $limit,
        'baseUrl'         => $view['router']->generate('mautic_leadnote_index', array('leadId' => $lead->getId(), 'page' => $page)),
        'sessionVar'      => 'leadnote'
    )); ?>
</div>
