<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\LeadBundle\Controller\Api;

use FOS\RestBundle\Util\Codes;
use JMS\Serializer\SerializationContext;
use Mautic\ApiBundle\Controller\CommonApiController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class ListApiController
 *
 * @package Mautic\LeadBundle\Controller\Api
 */
class ListApiController extends CommonApiController
{

    public function initialize (FilterControllerEvent $event)
    {
        parent::initialize($event);
        $this->model            = $this->factory->getModel('lead.list');
        $this->entityClass      = 'Mautic\LeadBundle\Entity\LeadList';
        $this->entityNameOne    = 'list';
        $this->entityNameMulti  = 'lists';
        $this->permissionBase   = 'lead:lists';
        $this->serializerGroups = array("leadListDetails", "userList", "publishDetails", "ipAddress");
    }

    /**
     * Obtains a list of smart lists for the user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getListsAction ()
    {
        $lists   = $this->factory->getModel('lead.list')->getUserLists();
        $view    = $this->view($lists, Codes::HTTP_OK);
        $context = SerializationContext::create()->setGroups(array('leadListList'));
        $view->setSerializationContext($context);

        return $this->handleView($view);
    }


    /**
     * Adds a lead to a list
     *
     * @param int $id     List ID
     * @param int $leadId Lead ID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function addLeadAction ($id, $leadId)
    {
        $entity = $this->model->getEntity($id);
        if (null !== $entity) {
            $leadModel = $this->factory->getModel('lead');
            $lead      = $leadModel->getEntity($leadId);

            // Does the lead exist and the user has permission to edit
            if ($lead == null) {
                return $this->notFound();
            } elseif (!$this->security->hasEntityAccess('lead:leads:editown', 'lead:leads:editother', $lead->getOwner())) {
                return $this->accessDenied();
            }

            // Does the user have access to the list
            $lists = $this->model->getUserLists();
            if (!isset($lists[$id])) {
                return $this->accessDenied();
            }

            $leadModel->addToLists($leadId, $entity);

            $view = $this->view(array('success' => 1), Codes::HTTP_OK);

            return $this->handleView($view);
        }

        return $this->notFound();
    }

    /**
     * Removes given lead from a list
     *
     * @param int $id     List ID
     * @param int $leadId Lead ID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function removeLeadAction ($id, $leadId)
    {
        $entity = $this->model->getEntity($id);
        if (null !== $entity) {
            $leadModel = $this->factory->getModel('lead');
            $lead      = $leadModel->getEntity($leadId);

            // Does the lead exist and the user has permission to edit
            if ($lead == null) {
                return $this->notFound();
            } elseif (!$this->security->hasEntityAccess('lead:leads:editown', 'lead:leads:editother', $lead->getOwner())) {
                return $this->accessDenied();
            }

            // Does the user have access to the list
            $lists = $this->model->getUserLists();
            if (!isset($lists[$id])) {
                return $this->accessDenied();
            }

            $leadModel->removeFromLists($leadId, $entity);

            $view = $this->view(array('success' => 1), Codes::HTTP_OK);

            return $this->handleView($view);
        }

        return $this->notFound();
    }
}