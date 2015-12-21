<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\FormBundle\Controller\Api;

use Mautic\ApiBundle\Controller\CommonApiController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class FormApiController
 */
class FormApiController extends CommonApiController
{

    /**
     * {@inheritdoc}
     */
    public function initialize (FilterControllerEvent $event)
    {
        parent::initialize($event);
        $this->model            = $this->factory->getModel('form');
        $this->entityClass      = 'Mautic\FormBundle\Entity\Form';
        $this->entityNameOne    = 'form';
        $this->entityNameMulti  = 'forms';
        $this->permissionBase   = 'form:forms';
        $this->serializerGroups = array('formDetails', 'categoryList', 'publishDetails');
    }

    /**
     * Obtains a list of forms
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getEntitiesAction ()
    {
        if (!$this->security->isGranted('form:forms:viewother')) {
            $this->listFilters = array(
                'column' => 'f.createdBy',
                'expr'   => 'eq',
                'value'  => $this->factory->getUser()->getId()
            );
        }

        return parent::getEntitiesAction();
    }

    /**
     * {@inheritdoc}
     */
    protected function preSerializeEntity (&$entity, $action = 'view')
    {
        $entity->automaticJs = '<script type="text/javascript" src="' . $this->generateUrl('mautic_form_generateform', array('id' => $entity->getId()), true) . '"></script>';
    }
}
