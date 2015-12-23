<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\EmailBundle\Controller\Api;

use FOS\RestBundle\Util\Codes;
use Mautic\ApiBundle\Controller\CommonApiController;
use Mautic\CoreBundle\Helper\InputHelper;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class EmailApiController
 *
 * @package Mautic\EmailBundle\Controller\Api
 */
class EmailApiController extends CommonApiController
{

    public function initialize (FilterControllerEvent $event)
    {
        parent::initialize($event);
        $this->model            = $this->factory->getModel('email');
        $this->entityClass      = 'Mautic\EmailBundle\Entity\Email';
        $this->entityNameOne    = 'email';
        $this->entityNameMulti  = 'emails';
        $this->permissionBase   = 'email:emails';
        $this->serializerGroups = array("emailDetails", "categoryList", "publishDetails", "assetList");
    }

    /**
     * Obtains a list of emails
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getEntitiesAction ()
    {
        if (!$this->security->isGranted('email:emails:viewother')) {
            $this->listFilters[] =
                array(
                    'column' => 'e.createdBy',
                    'expr'   => 'eq',
                    'value'  => $this->factory->getUser()->getId()
                );
        }

        //get parent level only
        $this->listFilters[] = array(
            'column' => 'e.variantParent',
            'expr'   => 'isNull'
        );

        return parent::getEntitiesAction();
    }

    /**
     * Sends the email to it's assigned lists
     *
     * @param int $id Email ID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function sendAction ($id)
    {
        $entity = $this->model->getEntity($id);
        if (null !== $entity) {
            if (!$this->checkEntityAccess($entity, 'view')) {
                return $this->accessDenied();
            }

            $lists = $this->request->request->get('lists', null);
            $limit = $this->request->request->get('limit', null);

            list($count, $failed) = $this->model->sendEmailToLists($entity, $lists, $limit);

            $view = $this->view(
                array(
                    'success'          => 1,
                    'sentCount'        => $count,
                    'failedRecipients' => $failed
                ),
                Codes::HTTP_OK
            );

            return $this->handleView($view);

        }

        return $this->notFound();
    }

    /**
     * Sends the email to a specific lead
     *
     * @param int $id     Email ID
     * @param int $leadId Lead ID
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function sendLeadAction ($id, $leadId)
    {
        $entity = $this->model->getEntity($id);
        if (null !== $entity) {
            if (!$this->checkEntityAccess($entity, 'view')) {
                return $this->accessDenied();
            }

            $leadModel = $this->factory->getModel('lead');
            $lead      = $leadModel->getEntity($leadId);

            if ($lead == null) {
                return $this->notFound();
            } elseif (!$this->security->hasEntityAccess('lead:leads:viewown', 'lead:leads:viewother', $lead->getOwner())) {
                return $this->accessDenied();
            }

            $post   = $this->request->request->all();
            $tokens = (!empty($post['tokens'])) ? $post['tokens'] : array();

            $cleantokens = array_map(function ($v) {
                return InputHelper::clean($v);
            }, $tokens);

            $leadFields = array_merge(array('id' => $leadId), $leadModel->flattenFields($lead->getFields()));

            $this->model->sendEmail($entity, $leadFields, array(
                'source' => array('api', 0),
                'tokens' => $cleantokens
            ));

            $view = $this->view(array('success' => 1), Codes::HTTP_OK);

            return $this->handleView($view);
        }

        return $this->notFound();
    }
}
