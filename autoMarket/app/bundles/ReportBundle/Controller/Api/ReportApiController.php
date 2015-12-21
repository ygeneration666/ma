<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\ReportBundle\Controller\Api;

use FOS\RestBundle\Util\Codes;
use JMS\Serializer\SerializationContext;
use Mautic\ApiBundle\Controller\CommonApiController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class ReportApiController
 */
class ReportApiController extends CommonApiController
{

    /**
     * {@inheritdoc}
     */
    public function initialize(FilterControllerEvent $event)
    {
        parent::initialize($event);
        $this->model            = $this->factory->getModel('report');
        $this->entityClass      = 'Mautic\ReportBundle\Entity\Report';
        $this->entityNameOne    = 'report';
        $this->entityNameMulti  = 'reports';
        $this->permissionBase   = 'report:reports';
        $this->serializerGroups = array('reportList', 'reportDetails');
    }

    /**
     * Obtains a list of reports
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getEntitiesAction()
    {
        if (!$this->security->isGranted('report:reports:viewother')) {
            $this->listFilters = array(
                'column' => 'r.createdBy',
                'expr'   => 'eq',
                'value'  => $this->factory->getUser()->getId()
            );
        }

        return parent::getEntitiesAction();
    }

    /**
     * Obtains a compiled report
     *
     * @param int $id Report ID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getReportAction($id)
    {
        $entity = $this->model->getEntity($id);

        if (!$entity instanceof $this->entityClass) {
            return $this->notFound();
        }

        $reportData = $this->model->getReportData($entity, $this->container->get('form.factory'), array('paginate' => false, 'ignoreGraphData' => true));

        // Unset keys that we don't need to send back
        foreach (array('graphs', 'contentTemplate', 'columns', 'limit') as $key) {
            unset($reportData[$key]);
        }

        $view = $this->view($reportData, Codes::HTTP_OK);

        return $this->handleView($view);
    }
}
