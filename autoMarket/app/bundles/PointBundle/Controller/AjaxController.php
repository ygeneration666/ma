<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\PointBundle\Controller;

use Mautic\CoreBundle\Controller\AjaxController as CommonAjaxController;
use Mautic\CoreBundle\Helper\InputHelper;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AjaxController
 */
class AjaxController extends CommonAjaxController
{

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function reorderTriggerEventsAction(Request $request)
    {
        $dataArray   = array('success' => 0);
        $session     = $this->factory->getSession();
        $triggerId   = InputHelper::clean($request->request->get('triggerId'));
        $sessionName = 'mautic.point.'.$triggerId.'.triggerevents.modified';
        $order       = InputHelper::clean($request->request->get('triggerEvent'));
        $components  = $session->get($sessionName);
        if (!empty($order) && !empty($components)) {
            $components = array_replace(array_flip($order), $components);
            $session->set($sessionName, $components);
            $dataArray['success'] = 1;
        }

        return $this->sendJsonResponse($dataArray);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function getActionFormAction(Request $request)
    {
        $dataArray = array(
            'success' => 0,
            'html'    => ''
        );
        $type      = InputHelper::clean($request->request->get('actionType'));

        if (!empty($type)) {
            //get the HTML for the form
            /** @var \Mautic\PointBundle\Model\PointModel $model */
            $model   = $this->factory->getModel('point');
            $actions = $model->getPointActions();

            if (isset($actions['actions'][$type])) {
                $themes = array('MauticPointBundle:FormTheme\Action');
                if (!empty($actions['actions'][$type]['formTheme'])) {
                    $themes[] = $actions['actions'][$type]['formTheme'];
                }

                $formType        = (!empty($actions['actions'][$type]['formType'])) ? $actions['actions'][$type]['formType'] : 'genericpoint_settings';
                $formTypeOptions = (!empty($actions['actions'][$type]['formTypeOptions'])) ? $actions['actions'][$type]['formTypeOptions'] : array();
                $form            = $this->get('form.factory')->create('pointaction', array(), array('formType' => $formType, 'formTypeOptions' => $formTypeOptions));
                $html            = $this->renderView('MauticPointBundle:Point:actionform.html.php', array(
                    'form' => $this->setFormTheme($form, 'MauticPointBundle:Point:actionform.html.php', $themes)
                ));

                //replace pointaction with point
                $html                 = str_replace('pointaction', 'point', $html);
                $dataArray['html']    = $html;
                $dataArray['success'] = 1;
            }
        }

        return $this->sendJsonResponse($dataArray);
    }
}
