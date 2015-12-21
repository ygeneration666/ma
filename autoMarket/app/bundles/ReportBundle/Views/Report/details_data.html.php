<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if ($tmpl == 'index')
    $view->extend('MauticReportBundle:Report:details.html.php');

$dataCount   = count($data);
$columnOrder = $report->getColumns();
$graphOrder  = $report->getGraphs();
$startCount  = ($dataCount > $limit) ? ($reportPage * $limit) - ($dataCount - 1) : 1;
?>

<?php if (!empty($columnOrder)): ?>
<!-- table section -->
<div class="panel panel-default bdr-t-wdh-0 mb-0">
    <div class="page-list"">
        <div class="table-responsive table-responsive-force">
            <table class="table table-hover table-striped table-bordered report-list" id="reportTable">
                <thead>
                <tr>
                    <th class="col-report-count"></th>
                    <?php foreach ($columnOrder as $key): ?>
                        <?php
                        if (isset($columns[$key])):
                            echo $view->render('MauticCoreBundle:Helper:tableheader.html.php', array(
                                'sessionVar' => 'report.' . $report->getId(),
                                'orderBy'    => $key,
                                'text'       => $columns[$key]['label'],
                                'class'      => 'col-report-' . $columns[$key]['type'],
                                'filterBy'   => $key,
                                'dataToggle' => in_array($columns[$key]['type'], array('date', 'datetime')) ? 'date' : '',
                                'target'     => '.report-content'
                            ));
                        else:
                            unset($columnOrder[$key]);
                        endif;
                        ?>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php if ($dataCount): ?>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td><?php echo $startCount; ?></td>
                            <?php foreach ($columnOrder as $key): ?>
                                <td><?php echo $view['formatter']->_($row[$columns[$key]['label']], $columns[$key]['type']); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php $startCount++; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td>&nbsp;</td>
                        <?php foreach ($columnOrder as $key): ?>
                            <td>&nbsp;</td>
                        <?php endforeach; ?>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <?php echo $view->render('MauticCoreBundle:Helper:pagination.html.php', array(
                'totalItems'      => $totalResults,
                'page'            => $reportPage,
                'limit'           => $limit,
                'baseUrl'         => $view['router']->generate('mautic_report_view', array(
                    'objectId' => $report->getId()
                )),
                'sessionVar'      => 'report.' . $report->getId(),
                'target'          => '.report-content'
            )); ?>
        </div>
    </div>
</div>
<!--/ table section -->
<?php endif; ?>

<?php if (!empty($graphOrder) && !empty($graphs)): ?>
<div class="mt-lg">
    <div class="row">
        <div class="pa-md">
            <div class="row equal">
            <?php
            $rowCount = 0;
            foreach ($graphOrder as $key):
                $details =  $graphs[$key];
                if ($rowCount >= 12):
                    echo '</div><div class="row equal">';
                    $rowCount = 0;
                endif;
                echo $view->render('MauticReportBundle:Graph:'.ucfirst($details['type']).'.html.php', array('graph' => $details['data'], 'options' => $details['options'], 'report' => $report));
                $rowCount += ($details['type'] == 'line') ? 12 : 4;
            endforeach;
            ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>