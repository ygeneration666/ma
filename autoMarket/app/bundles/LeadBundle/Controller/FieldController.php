<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\LeadBundle\Controller;

use Mautic\CoreBundle\Controller\FormController;
use Mautic\LeadBundle\Entity\LeadField;
use Symfony\Component\Form\FormError;

class FieldController extends FormController
{

    /**
     * Generate's default list view
     *
     * @param int $page
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($page = 1)
    {
        //set some permissions
        $permissions = $this->factory->getSecurity()->isGranted(array('lead:fields:full'), 'RETURN_ARRAY');

        $session = $this->factory->getSession();

        if (!$permissions['lead:fields:full']) {
            return $this->accessDenied();
        }

        if ($this->request->getMethod() == 'POST') {
            $this->setListFilters();
        }

        $limit = $session->get('mautic.leadfield.limit', $this->factory->getParameter('default_pagelimit'));
        $search = $this->request->get('search', $session->get('mautic.leadfield.filter', ''));
        $session->set('mautic.leadfilter.filter', $search);

        //do some default filtering
        $orderBy    = $this->factory->getSession()->get('mautic.leadfilter.orderby', 'f.order');
        $orderByDir = $this->factory->getSession()->get('mautic.leadfilter.orderbydir', 'ASC');

        $start = ($page === 1) ? 0 : (($page - 1) * $limit);
        if ($start < 0) {
            $start = 0;
        }

        $request = $this->factory->getRequest();
        $search  = $request->get('search', $session->get('mautic.lead.emailtoken.filter', ''));

        $session->set('mautic.lead.emailtoken.filter', $search);

        $fields = $this->factory->getModel('lead.field')->getEntities(array(
            'start'          => $start,
            'limit'          => $limit,
            'filter'         => array('string' => $search),
            'orderBy'        => $orderBy,
            'orderByDir'     => $orderByDir
        ));
        $count  = count($fields);

        if ($count && $count < ($start + 1)) {
            //the number of entities are now less then the current page so redirect to the last page
            if ($count === 1) {
                $lastPage = 1;
            } else {
                $lastPage = (ceil($count / $limit)) ?: 1;
            }
            $session->set('mautic.leadfield.page', $lastPage);
            $returnUrl = $this->generateUrl('mautic_leadfield_index', array('page' => $lastPage));

            return $this->postActionRedirect(array(
                'returnUrl'       => $returnUrl,
                'viewParameters'  => array('page' => $lastPage),
                'contentTemplate' => 'MauticLeadBundle:Field:index',
                'passthroughVars' => array(
                    'activeLink'    => '#mautic_leadfield_index',
                    'mauticContent' => 'leadfield'
                )
            ));
        }

        //set what page currently on so that we can return here after form submission/cancellation
        $session->set('mautic.leadfield.page', $page);

        $tmpl = $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index';

        return $this->delegateView(array(
            'viewParameters'  => array(
                'items'       => $fields,
                'searchValue' => $search,
                'permissions' => $permissions,
                'tmpl'        => $tmpl,
                'totalItems'  => $count,
                'limit'       => $limit,
                'page'        => $page
            ),
            'contentTemplate' => 'MauticLeadBundle:Field:list.html.php',
            'passthroughVars' => array(
                'activeLink'    => '#mautic_leadfield_index',
                'route'         => $this->generateUrl('mautic_leadfield_index', array('page' => $page)),
                'mauticContent' => 'leadfield'
            )
        ));
    }

    /**
     * Generate's new form and processes post data
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction ()
    {
        if (!$this->factory->getSecurity()->isGranted('lead:fields:full')) {
            return $this->accessDenied();
        }

        //retrieve the entity
        $field     = new LeadField();
        $model      = $this->factory->getModel('lead.field');
        //set the return URL for post actions
        $returnUrl  = $this->generateUrl('mautic_leadfield_index');
        $action     = $this->generateUrl('mautic_leadfield_action', array('objectAction' => 'new'));
        //get the user form factory
        $form       = $model->createForm($field, $this->get('form.factory'), $action);

        ///Check for a submitted form and process it
        if ($this->request->getMethod() == 'POST') {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    $request = $this->request->request->all();
                    if (isset($request['leadfield']['properties'])) {
                        $result = $model->setFieldProperties($field, $request['leadfield']['properties']);
                        if ($result !== true) {
                            //set the error
                            $form->get('properties')->addError(new FormError(
                                $this->get('translator')->trans($result, array(), 'validators')
                            ));
                            $valid = false;
                        }
                    }

                    if ($valid) {
                        try {
                            //form is valid so process the data
                            $model->saveEntity($field);

                            $this->addFlash('mautic.core.notice.created', array(
                                '%name%'      => $field->getLabel(),
                                '%menu_link%' => 'mautic_leadfield_index',
                                '%url%'       => $this->generateUrl('mautic_leadfield_action', array(
                                    'objectAction' => 'edit',
                                    'objectId'     => $field->getId()
                                ))
                            ));
                        } catch (\Exception $e) {
                            $form['alias']->addError(new FormError($this->get('translator')->trans('mautic.lead.field.failed', array('%error%' => $e->getMessage()), 'validators')));
                            $valid = false;
                        }
                    }
                }
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                return $this->postActionRedirect(array(
                    'returnUrl'       => $returnUrl,
                    'contentTemplate' => 'MauticLeadBundle:Field:index',
                    'passthroughVars' => array(
                        'activeLink'    => '#mautic_leadfield_index',
                        'mauticContent' => 'leadfield'
                    )
                ));
            } elseif ($valid && !$cancelled) {
                return $this->editAction($field->getId(), true);
            }
        }

        return $this->delegateView(array(
            'viewParameters'  => array(
                'form'            => $form->createView()
            ),
            'contentTemplate' => 'MauticLeadBundle:Field:form.html.php',
            'passthroughVars' => array(
                'activeLink'    => '#mautic_leadfield_index',
                'route'         => $this->generateUrl('mautic_leadfield_action', array('objectAction' => 'new')),
                'mauticContent' => 'leadfield'
            )
        ));
    }

    /**
     * Generate's edit form and processes post data
     *
     * @param            $objectId
     * @param bool|false $ignorePost
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction ($objectId, $ignorePost = false)
    {
        if (!$this->factory->getSecurity()->isGranted('lead:fields:full')) {
            return $this->accessDenied();
        }

        $model   = $this->factory->getModel('lead.field');
        $field   = $model->getEntity($objectId);

        //set the return URL
        $returnUrl  = $this->generateUrl('mautic_leadfield_index');

        $postActionVars = array(
            'returnUrl'       => $returnUrl,
            'contentTemplate' => 'MauticLeadBundle:Field:index',
            'passthroughVars' => array(
                'activeLink'    => '#mautic_leadfield_index',
                'mauticContent' => 'leadfield'
            )
        );
        //list not found
        if ($field === null) {
            return $this->postActionRedirect(
                array_merge($postActionVars, array(
                    'flashes' => array(
                        array(
                            'type' => 'error',
                            'msg'  => 'mautic.lead.field.error.notfound',
                            'msgVars' => array('%id%' => $objectId)
                        )
                    )
                ))
            );
        } elseif ($model->isLocked($field)) {
            //deny access if the entity is locked
            return $this->isLocked($postActionVars, $field, 'lead.field');
        }

        $action = $this->generateUrl('mautic_leadfield_action', array('objectAction' => 'edit', 'objectId' => $objectId));
        $form   = $model->createForm($field, $this->get('form.factory'), $action);

        ///Check for a submitted form and process it
        if (!$ignorePost && $this->request->getMethod() == 'POST') {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    $request = $this->request->request->all();
                    if (isset($request['leadfield']['properties'])) {
                        $result = $model->setFieldProperties($field, $request['leadfield']['properties']);
                        if ($result !== true) {
                            //set the error
                            $form->get('properties')->addError(new FormError(
                                $this->get('translator')->trans($result, array(), 'validators')
                            ));
                            $valid = false;
                        }
                    }

                    if ($valid) {
                        //form is valid so process the data
                        $model->saveEntity($field, $form->get('buttons')->get('save')->isClicked());

                        $this->addFlash('mautic.core.notice.updated',  array(
                            '%name%'      => $field->getLabel(),
                            '%menu_link%' => 'mautic_leadfield_index',
                            '%url%'       => $this->generateUrl('mautic_leadfield_action', array(
                                'objectAction' => 'edit',
                                'objectId'     => $field->getId()
                            ))
                        ));
                    }
                }
            } else {
                //unlock the entity
                $model->unlockEntity($field);
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                return $this->postActionRedirect(
                    array_merge($postActionVars, array(
                            'viewParameters'  => array('objectId' => $field->getId()),
                            'contentTemplate' => 'MauticLeadBundle:Field:index'
                        )
                    )
                );
            }
        } else {
            //lock the entity
            $model->lockEntity($field);
        }

        return $this->delegateView(array(
            'viewParameters'  => array(
                'form'    => $form->createView()
            ),
            'contentTemplate' => 'MauticLeadBundle:Field:form.html.php',
            'passthroughVars' => array(
                'activeLink'    => '#mautic_leadfield_index',
                'route'         => $action,
                'mauticContent' => 'leadfield'
            )
        ));
    }

    /**
     * Clone an entity
     *
     * @param $objectId
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function cloneAction ($objectId)
    {
        $model   = $this->factory->getModel('lead.field');
        $entity  = $model->getEntity($objectId);

        if ($entity != null) {
            if (!$this->factory->getSecurity()->isGranted('lead:fields:full')) {
                return $this->accessDenied();
            }

            $clone = clone $entity;
            $clone->setIsPublished(false);
            $clone->setIsFixed(false);
            $model->saveEntity($clone);
            $objectId = $clone->getId();
        }

        return $this->editAction($objectId);
    }

    /**
     * Delete a field
     *
     * @param         $objectId
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($objectId)
    {
        if (!$this->factory->getSecurity()->isGranted('lead:fields:full')) {
            return $this->accessDenied();
        }

        $returnUrl = $this->generateUrl('mautic_leadfield_index');
        $flashes   = array();

        $postActionVars = array(
            'returnUrl'       => $returnUrl,
            'contentTemplate' => 'MauticLeadBundle:Field:index',
            'passthroughVars' => array(
                'activeLink'    => '#mautic_leadfield_index',
                'mauticContent' => 'lead'
            )
        );

        if ($this->request->getMethod() == 'POST') {
            $model  = $this->factory->getModel('lead.field');
            $field = $model->getEntity($objectId);

            if ($field === null) {
                $flashes[] = array(
                    'type'    => 'error',
                    'msg'     => 'mautic.lead.field.error.notfound',
                    'msgVars' => array('%id%' => $objectId)
                );
            } elseif ($model->isLocked($field)) {
                return $this->isLocked($postActionVars, $field, 'lead.field');
            } elseif ($field->isFixed()) {
                //cannot delete fixed fields
                return $this->accessDenied();
            }

            $model->deleteEntity($field);

            $flashes[]  = array(
                'type'    => 'notice',
                'msg'     => 'mautic.core.notice.deleted',
                'msgVars' => array(
                    '%name%' => $field->getLabel(),
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
    public function batchDeleteAction() {
        if (!$this->factory->getSecurity()->isGranted('lead:fields:full')) {
            return $this->accessDenied();
        }

        $returnUrl = $this->generateUrl('mautic_leadfield_index');
        $flashes     = array();

        $postActionVars = array(
            'returnUrl'       => $returnUrl,
            'contentTemplate' => 'MauticLeadBundle:Field:index',
            'passthroughVars' => array(
                'activeLink'    => '#mautic_leadfield_index',
                'mauticContent' => 'lead'
            )
        );

        if ($this->request->getMethod() == 'POST') {
            $model     = $this->factory->getModel('lead.field');
            $ids       = json_decode($this->request->query->get('ids', '{}'));
            $deleteIds = array();

            // Loop over the IDs to perform access checks pre-delete
            foreach ($ids as $objectId) {
                $entity = $model->getEntity($objectId);

                if ($entity === null) {
                    $flashes[] = array(
                        'type'    => 'error',
                        'msg'     => 'mautic.lead.field.error.notfound',
                        'msgVars' => array('%id%' => $objectId)
                    );
                } elseif ($entity->isFixed()) {
                    $flashes[] = $this->accessDenied(true);
                } elseif ($model->isLocked($entity)) {
                    $flashes[] = $this->isLocked($postActionVars, $entity, 'lead.field', true);
                } else {
                    $deleteIds[] = $objectId;
                }
            }

            // Delete everything we are able to
            if (!empty($deleteIds)) {
                $entities = $model->deleteEntities($deleteIds);

                $flashes[] = array(
                    'type' => 'notice',
                    'msg'  => 'mautic.lead.field.notice.batch_deleted',
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
