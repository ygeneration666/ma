<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\PointBundle\Controller;

use Mautic\CoreBundle\Controller\FormController;
use Mautic\PointBundle\Entity\Point;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PointController
 */
class PointController extends FormController
{
    /**
     * @param int $page
     *
     * @return JsonResponse|Response
     */
    public function indexAction($page = 1)
    {
        //set some permissions
        $permissions = $this->factory->getSecurity()->isGranted(array(
            'point:points:view',
            'point:points:create',
            'point:points:edit',
            'point:points:delete',
            'point:points:publish'
        ), "RETURN_ARRAY");

        if (!$permissions['point:points:view']) {
            return $this->accessDenied();
        }

        if ($this->request->getMethod() == 'POST') {
            $this->setListFilters();
        }

        //set limits
        $limit = $this->factory->getSession()->get('mautic.point.limit', $this->factory->getParameter('default_pagelimit'));
        $start = ($page === 1) ? 0 : (($page - 1) * $limit);
        if ($start < 0) {
            $start = 0;
        }

        $search = $this->request->get('search', $this->factory->getSession()->get('mautic.point.filter', ''));
        $this->factory->getSession()->set('mautic.point.filter', $search);

        $filter     = array('string' => $search, 'force' => array());
        $orderBy    = $this->factory->getSession()->get('mautic.point.orderby', 'p.name');
        $orderByDir = $this->factory->getSession()->get('mautic.point.orderbydir', 'ASC');

        $points = $this->factory->getModel('point')->getEntities(array(
            'start'      => $start,
            'limit'      => $limit,
            'filter'     => $filter,
            'orderBy'    => $orderBy,
            'orderByDir' => $orderByDir
        ));

        $count = count($points);
        if ($count && $count < ($start + 1)) {
            $lastPage = ($count === 1) ? 1 : (ceil($count / $limit)) ?: 1;
            $this->factory->getSession()->set('mautic.point.page', $lastPage);
            $returnUrl   = $this->generateUrl('mautic_point_index', array('page' => $lastPage));

            return $this->postActionRedirect(array(
                'returnUrl'       => $returnUrl,
                'viewParameters'  => array('page' => $lastPage),
                'contentTemplate' => 'MauticPointBundle:Point:index',
                'passthroughVars' => array(
                    'activeLink'    => '#mautic_point_index',
                    'mauticContent' => 'point'
                )
            ));
        }

        //set what page currently on so that we can return here after form submission/cancellation
        $this->factory->getSession()->set('mautic.point.page', $page);

        $tmpl = $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index';

        //get the list of actions
        $actions = $this->factory->getModel('point')->getPointActions();

        return $this->delegateView(array(
            'viewParameters'  => array(
                'searchValue' => $search,
                'items'       => $points,
                'actions'     => $actions['actions'],
                'page'        => $page,
                'limit'       => $limit,
                'permissions' => $permissions,
                'tmpl'        => $tmpl
            ),
            'contentTemplate' => 'MauticPointBundle:Point:list.html.php',
            'passthroughVars' => array(
                'activeLink'     => '#mautic_point_index',
                'mauticContent'  => 'point',
                'route'          => $this->generateUrl('mautic_point_index', array('page' => $page))
            )
        ));
    }

    /**
     * Generates new form and processes post data
     *
     * @param  \Mautic\PointBundle\Entity\Point $entity
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newAction($entity = null)
    {
        $model   = $this->factory->getModel('point');

        if (!($entity instanceof Point)) {
            /** @var \Mautic\PointBundle\Entity\Point $entity */
            $entity  = $model->getEntity();
        }

        if (!$this->factory->getSecurity()->isGranted('point:points:create')) {
            return $this->accessDenied();
        }

        //set the page we came from
        $page = $this->factory->getSession()->get('mautic.point.page', 1);

        $actionType = ($this->request->getMethod() == 'POST') ? $this->request->request->get('point[type]', '', true) : '';

        $action  = $this->generateUrl('mautic_point_action', array('objectAction' => 'new'));
        $actions = $model->getPointActions();
        $form    = $model->createForm($entity, $this->get('form.factory'), $action, array(
            'pointActions' => $actions,
            'actionType'   => $actionType
        ));
        $viewParameters  = array('page' => $page);

        ///Check for a submitted form and process it
        if ($this->request->getMethod() == 'POST') {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    //form is valid so process the data
                    $model->saveEntity($entity);

                    $this->addFlash('mautic.core.notice.created', array(
                        '%name%'      => $entity->getName(),
                        '%menu_link%' => 'mautic_point_index',
                        '%url%'       => $this->generateUrl('mautic_point_action', array(
                            'objectAction' => 'edit',
                            'objectId'     => $entity->getId()
                        ))
                    ));

                    if ($form->get('buttons')->get('save')->isClicked()) {
                        $returnUrl      = $this->generateUrl('mautic_point_index', $viewParameters);
                        $template       = 'MauticPointBundle:Point:index';
                    } else {
                        //return edit view so that all the session stuff is loaded
                        return $this->editAction($entity->getId(), true);
                    }
                }
            } else {
                $returnUrl = $this->generateUrl('mautic_point_index', $viewParameters);
                $template  = 'MauticPointBundle:Point:index';
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                return $this->postActionRedirect(array(
                    'returnUrl'       => $returnUrl,
                    'viewParameters'  => $viewParameters,
                    'contentTemplate' => $template,
                    'passthroughVars' => array(
                        'activeLink'    => '#mautic_point_index',
                        'mauticContent' => 'point'
                    )
                ));
            }
        }

        $themes = array('MauticPointBundle:FormTheme\Action');
        if ($actionType && !empty($actions['actions'][$actionType]['formTheme'])) {
            $themes[] = $actions['actions'][$actionType]['formTheme'];
        }

        return $this->delegateView(array(
            'viewParameters'  => array(
                'tmpl'           => $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index',
                'entity'         => $entity,
                'form'           => $this->setFormTheme($form, 'MauticPointBundle:Point:form.html.php', $themes),
                'actions'        => $actions['actions']
            ),
            'contentTemplate' => 'MauticPointBundle:Point:form.html.php',
            'passthroughVars' => array(
                'activeLink'    => '#mautic_point_index',
                'mauticContent' => 'point',
                'route'         => $this->generateUrl('mautic_point_action', array(
                        'objectAction' => (!empty($valid) ? 'edit' : 'new'), //valid means a new form was applied
                        'objectId'     => $entity->getId()
                    )
                )
            )
        ));
    }

    /**
     * Generates edit form and processes post data
     *
     * @param int  $objectId
     * @param bool $ignorePost
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction($objectId, $ignorePost = false)
    {
        $model  = $this->factory->getModel('point');
        $entity = $model->getEntity($objectId);

        //set the page we came from
        $page = $this->factory->getSession()->get('mautic.point.page', 1);

        $viewParameters = array('page' => $page);

        //set the return URL
        $returnUrl      = $this->generateUrl('mautic_point_index', array('page' => $page));

        $postActionVars = array(
            'returnUrl'       => $returnUrl,
            'viewParameters'  => $viewParameters,
            'contentTemplate' => 'MauticPointBundle:Point:index',
            'passthroughVars' => array(
                'activeLink'    => '#mautic_point_index',
                'mauticContent' => 'point'
            )
        );

        //form not found
        if ($entity === null) {
            return $this->postActionRedirect(
                array_merge($postActionVars, array(
                    'flashes' => array(
                        array(
                            'type' => 'error',
                            'msg'  => 'mautic.point.error.notfound',
                            'msgVars' => array('%id%' => $objectId)
                        )
                    )
                ))
            );
        } elseif (!$this->factory->getSecurity()->isGranted('point:points:edit')) {
            return $this->accessDenied();
        } elseif ($model->isLocked($entity)) {
            //deny access if the entity is locked
            return $this->isLocked($postActionVars, $entity, 'point');
        }

        $actionType = ($this->request->getMethod() == 'POST') ? $this->request->request->get('point[type]', '', true) : $entity->getType();

        $action  = $this->generateUrl('mautic_point_action', array('objectAction' => 'edit', 'objectId' => $objectId));
        $actions = $model->getPointActions();
        $form    = $model->createForm($entity, $this->get('form.factory'), $action, array(
            'pointActions' => $actions,
            'actionType'   => $actionType
        ));

        ///Check for a submitted form and process it
        if (!$ignorePost && $this->request->getMethod() == 'POST') {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    //form is valid so process the data
                    $model->saveEntity($entity, $form->get('buttons')->get('save')->isClicked());

                    $this->addFlash('mautic.core.notice.updated', array(
                        '%name%'      => $entity->getName(),
                        '%menu_link%' => 'mautic_point_index',
                        '%url%'       => $this->generateUrl('mautic_point_action', array(
                            'objectAction' => 'edit',
                            'objectId'     => $entity->getId()
                        ))
                    ));

                    if ($form->get('buttons')->get('save')->isClicked()) {
                        $returnUrl = $this->generateUrl('mautic_point_index', $viewParameters);
                        $template  = 'MauticPointBundle:Point:index';
                    }
                }
            } else {
                //unlock the entity
                $model->unlockEntity($entity);

                $returnUrl = $this->generateUrl('mautic_point_index', $viewParameters);
                $template  = 'MauticPointBundle:Point:index';
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                return $this->postActionRedirect(
                    array_merge($postActionVars, array(
                        'returnUrl'       => $returnUrl,
                        'viewParameters'  => $viewParameters,
                        'contentTemplate' => $template
                    ))
                );
            }
        } else {
            //lock the entity
            $model->lockEntity($entity);
        }

        $themes = array('MauticPointBundle:FormTheme\Action');
        if (!empty($actions['actions'][$actionType]['formTheme'])) {
            $themes[] = $actions['actions'][$actionType]['formTheme'];
        }
        return $this->delegateView(array(
            'viewParameters'  => array(
                'tmpl'           => $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index',
                'entity'         => $entity,
                'form'           => $this->setFormTheme($form, 'MauticPointBundle:Point:form.html.php', $themes),
                'actions'        => $actions['actions']
            ),
            'contentTemplate' => 'MauticPointBundle:Point:form.html.php',
            'passthroughVars' => array(
                'activeLink'    => '#mautic_point_index',
                'mauticContent' => 'point',
                'route'         => $this->generateUrl('mautic_point_action', array(
                        'objectAction' => 'edit',
                        'objectId'     => $entity->getId()
                    )
                )
            )
        ));
    }

    /**
     * Clone an entity
     *
     * @param int $objectId
     *
     * @return array|JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function cloneAction($objectId)
    {
        $model   = $this->factory->getModel('point');
        $entity  = $model->getEntity($objectId);

        if ($entity != null) {
            if (!$this->factory->getSecurity()->isGranted('point:points:create')) {
                return $this->accessDenied();
            }

            $entity = clone $entity;
            $entity->setIsPublished(false);
        }

        return $this->newAction($entity);
    }

    /**
     * Deletes the entity
     *
     * @param int $objectId
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($objectId)
    {
        $page      = $this->factory->getSession()->get('mautic.point.page', 1);
        $returnUrl = $this->generateUrl('mautic_point_index', array('page' => $page));
        $flashes   = array();

        $postActionVars = array(
            'returnUrl'       => $returnUrl,
            'viewParameters'  => array('page' => $page),
            'contentTemplate' => 'MauticPointBundle:Point:index',
            'passthroughVars' => array(
                'activeLink'    => '#mautic_point_index',
                'mauticContent' => 'point'
            )
        );

        if ($this->request->getMethod() == 'POST') {
            $model  = $this->factory->getModel('point');
            $entity = $model->getEntity($objectId);

            if ($entity === null) {
                $flashes[] = array(
                    'type'    => 'error',
                    'msg'     => 'mautic.point.error.notfound',
                    'msgVars' => array('%id%' => $objectId)
                );
            } elseif (!$this->factory->getSecurity()->isGranted('point:points:delete')) {
                return $this->accessDenied();
            } elseif ($model->isLocked($entity)) {
                return $this->isLocked($postActionVars, $entity, 'point');
            }

            $model->deleteEntity($entity);

            $identifier = $this->get('translator')->trans($entity->getName());
            $flashes[] = array(
                'type' => 'notice',
                'msg'  => 'mautic.core.notice.deleted',
                'msgVars' => array(
                    '%name%' => $identifier,
                    '%id%'   => $objectId
                )
            );
        } //else don't do anything

        return $this->postActionRedirect(
            array_merge($postActionVars, array(
                'flashes' => $flashes
            ))
        );
    }

    /**
     * Deletes a group of entities
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function batchDeleteAction()
    {
        $page      = $this->factory->getSession()->get('mautic.point.page', 1);
        $returnUrl = $this->generateUrl('mautic_point_index', array('page' => $page));
        $flashes   = array();

        $postActionVars = array(
            'returnUrl'       => $returnUrl,
            'viewParameters'  => array('page' => $page),
            'contentTemplate' => 'MauticPointBundle:Point:index',
            'passthroughVars' => array(
                'activeLink'    => '#mautic_point_index',
                'mauticContent' => 'point'
            )
        );

        if ($this->request->getMethod() == 'POST') {
            $model     = $this->factory->getModel('point');
            $ids       = json_decode($this->request->query->get('ids', '{}'));
            $deleteIds = array();

            // Loop over the IDs to perform access checks pre-delete
            foreach ($ids as $objectId) {
                $entity = $model->getEntity($objectId);

                if ($entity === null) {
                    $flashes[] = array(
                        'type'    => 'error',
                        'msg'     => 'mautic.point.error.notfound',
                        'msgVars' => array('%id%' => $objectId)
                    );
                } elseif (!$this->factory->getSecurity()->isGranted('point:points:delete')) {
                    $flashes[] = $this->accessDenied(true);
                } elseif ($model->isLocked($entity)) {
                    $flashes[] = $this->isLocked($postActionVars, $entity, 'point', true);
                } else {
                    $deleteIds[] = $objectId;
                }
            }

            // Delete everything we are able to
            if (!empty($deleteIds)) {
                $entities = $model->deleteEntities($deleteIds);

                $flashes[] = array(
                    'type' => 'notice',
                    'msg'  => 'mautic.point.notice.batch_deleted',
                    'msgVars' => array(
                        '%count%' => count($entities)
                    )
                );
            }
        } //else don't do anything

        return $this->postActionRedirect(
            array_merge($postActionVars, array(
                'flashes' => $flashes
            ))
        );
    }
}
