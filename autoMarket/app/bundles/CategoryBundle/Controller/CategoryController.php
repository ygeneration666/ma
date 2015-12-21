<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CategoryBundle\Controller;

use Mautic\CoreBundle\Controller\FormController;
use Mautic\CategoryBundle\CategoryEvents;
use Mautic\CategoryBundle\Event\CategoryTypesEvent;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoryController extends FormController
{

    /**
     * @param        $bundle
     * @param        $objectAction
     * @param int    $objectId
     * @param string $objectModel
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executeCategoryAction ($bundle, $objectAction, $objectId = 0, $objectModel = '')
    {
        if (method_exists($this, "{$objectAction}Action")) {
            return $this->{"{$objectAction}Action"}($bundle, $objectId, $objectModel);
        } else {
            return $this->accessDenied();
        }
    }

    /**
     * @param     $bundle
     * @param int $page
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction ($bundle, $page = 1)
    {
        $session = $this->factory->getSession();

        $search = $this->request->get('search', $session->get('mautic.category.filter', ''));
        $bundle = $this->request->get('bundle', $session->get('mautic.category.type', ''));

        if ($bundle) {
            $session->set('mautic.category.type', $bundle);
        }

        // hack to make pagination work for default list view
        if ($bundle == 'all') {
            $bundle = 'category';
        }

        $session->set('mautic.category.filter', $search);

        //set some permissions
        $permissions = $this->factory->getSecurity()->isGranted(array(
            $bundle . ':categories:view',
            $bundle . ':categories:create',
            $bundle . ':categories:edit',
            $bundle . ':categories:delete'
        ), "RETURN_ARRAY");

        if (!$permissions[$bundle . ':categories:view']) {
            return $this->accessDenied();
        }

        if ($this->request->getMethod() == 'POST') {
            $this->setListFilters();
        }

        $viewParams = array(
            'page'   => $page,
            'bundle' => $bundle
        );

        //set limits
        $limit = $session->get('mautic.category.limit', $this->factory->getParameter('default_pagelimit'));
        $start = ($page === 1) ? 0 : (($page - 1) * $limit);
        if ($start < 0) {
            $start = 0;
        }

        if ($bundle == 'category') {
            $bundleFilter = null;
        } else {
            $bundleFilter = array(
                'column' => 'c.bundle',
                'expr'   => 'eq',
                'value'  => $bundle
            );
        }

        $filter = array('string' => $search, 'force' => array($bundleFilter));

        $orderBy    = $this->factory->getSession()->get('mautic.category.orderby', 'c.title');
        $orderByDir = $this->factory->getSession()->get('mautic.category.orderbydir', 'DESC');

        $entities = $this->factory->getModel('category.category')->getEntities(
            array(
                'start'      => $start,
                'limit'      => $limit,
                'filter'     => $filter,
                'orderBy'    => $orderBy,
                'orderByDir' => $orderByDir
            )
        );

        $count = count($entities);
        if ($count && $count < ($start + 1)) {
            //the number of entities are now less then the current page so redirect to the last page
            if ($count === 1) {
                $lastPage = 1;
            } else {
                $lastPage = (ceil($count / $limit)) ?: 1;
            }
            $viewParams['page'] = $lastPage;
            $session->set('mautic.category.page', $lastPage);
            $returnUrl = $this->generateUrl('mautic_category_index', $viewParams);

            return $this->postActionRedirect(array(
                'returnUrl'       => $returnUrl,
                'viewParameters'  => array('page' => $lastPage),
                'contentTemplate' => 'MauticCategoryBundle:Category:index',
                'passthroughVars' => array(
                    'activeLink'    => '#mautic_' . $bundle . 'category_index',
                    'mauticContent' => 'category'
                )
            ));
        }

        $categoryTypes = array('category' => $this->get('translator')->trans('mautic.core.select'));

        $dispatcher = $this->factory->getDispatcher();
        if ($dispatcher->hasListeners(CategoryEvents::CATEGORY_ON_BUNDLE_LIST_BUILD)) {
            $event = new CategoryTypesEvent;
            $dispatcher->dispatch(CategoryEvents::CATEGORY_ON_BUNDLE_LIST_BUILD, $event);
            $categoryTypes = array_merge($categoryTypes, $event->getCategoryTypes());
        }

        //set what page currently on so that we can return here after form submission/cancellation
        $session->set('mautic.category.page', $page);

        $tmpl = $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index';

        return $this->delegateView(array(
            'returnUrl'       => $this->generateUrl('mautic_category_index', $viewParams),
            'viewParameters'    => array(
                'bundle'        => $bundle,
                'searchValue'   => $search,
                'items'         => $entities,
                'page'          => $page,
                'limit'         => $limit,
                'permissions'   => $permissions,
                'tmpl'          => $tmpl,
                'categoryTypes' => $categoryTypes
            ),
            'contentTemplate' => 'MauticCategoryBundle:Category:list.html.php',
            'passthroughVars' => array(
                'activeLink'    => '#mautic_' . $bundle . 'category_index',
                'mauticContent' => 'category',
                'route'         => $this->generateUrl('mautic_category_index', $viewParams)
            )
        ));
    }

    /**
     * Generates new form and processes post data
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction ($bundle)
    {
        $session   = $this->factory->getSession();
        $model     = $this->factory->getModel('category');
        $entity    = $model->getEntity();
        $success   = $closeModal = 0;
        $cancelled = $valid = false;
        $method    = $this->request->getMethod();
        $inForm    = ($method == 'POST') ? $this->request->request->get('category_form[inForm]', 0, true) : $this->request->get('inForm', 0);
        $showSelect = $this->request->get('show_bundle_select', false);

        //not found
        if (!$this->factory->getSecurity()->isGranted($bundle . ':categories:create')) {
            return $this->modalAccessDenied();
        }
        //Create the form
        $action = $this->generateUrl('mautic_category_action', array(
            'objectAction' => 'new',
            'bundle'       => $bundle
        ));
        $form   = $model->createForm($entity, $this->get('form.factory'), $action, array('bundle' => $bundle, 'show_bundle_select' => $showSelect));
        $form['inForm']->setData($inForm);
        ///Check for a submitted form and process it
        if ($method == 'POST') {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    $success = 1;

                    //form is valid so process the data
                    $model->saveEntity($entity, $form->get('buttons')->get('save')->isClicked());

                    $this->addFlash('mautic.category.notice.created', array(
                        '%name%' => $entity->getName()
                    ));
                }
            } else {
                $success = 1;
            }
        }

        $closeModal = ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked()));

        if ($closeModal) {
            if ($inForm) {
                return new JsonResponse(array(
                    'mauticContent' => 'category',
                    'closeModal'    => 1,
                    'inForm'        => 1,
                    'categoryName'  => $entity->getName(),
                    'categoryId'    => $entity->getId()
                ));
            }

            $viewParameters = array(
                'page'   => $session->get('mautic.category.page'),
                'bundle' => $bundle
            );

            return $this->postActionRedirect(array(
                'returnUrl'       => $this->generateUrl('mautic_category_index', $viewParameters),
                'viewParameters'  => $viewParameters,
                'contentTemplate' => 'MauticCategoryBundle:Category:index',
                'passthroughVars' => array(
                    'activeLink'    => '#mautic_' . $bundle . 'category_index',
                    'mauticContent' => 'category',
                    'closeModal'    => 1
                )
            ));
        } elseif (!empty($valid)) {

            //return edit view to prevent duplicates
            return $this->editAction($bundle, $entity->getId(), true);

        } else {
            return $this->ajaxAction(array(
                'contentTemplate' => 'MauticCategoryBundle:Category:form.html.php',
                'viewParameters'  => array(
                    'form'           => $form->createView(),
                    'activeCategory' => $entity,
                    'bundle'         => $bundle
                ),
                'passthroughVars' => array(
                    'mauticContent' => 'category',
                    'success'       => $success,
                    'route'         => false
                )
            ));
        }
    }

    /**
     * Generates edit form and processes post data
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction ($bundle, $objectId, $ignorePost = false)
    {
        $session   = $this->factory->getSession();
        $model     = $this->factory->getModel('category');
        $entity    = $model->getEntity($objectId);
        $success   = $closeModal = 0;
        $cancelled = $valid = false;
        $method    = $this->request->getMethod();
        $inForm    = ($method == 'POST') ? $this->request->request->get('category_form[inForm]', 0, true) : $this->request->get('inForm', 0);

        //not found
        if ($entity === null) {
            $closeModal = true;
        } elseif (!$this->factory->getSecurity()->isGranted($bundle . ':categories:view')) {
            return $this->modalAccessDenied();
        } elseif ($model->isLocked($entity)) {
            return $this->modalAccessDenied();
        }

        //Create the form
        $action = $this->generateUrl('mautic_category_action', array(
            'objectAction' => 'edit',
            'objectId'     => $objectId,
            'bundle'       => $bundle
        ));
        $form   = $model->createForm($entity, $this->get('form.factory'), $action, array('bundle' => $bundle));
        $form['inForm']->setData($inForm);

        ///Check for a submitted form and process it
        if (!$ignorePost && $method == 'POST') {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    $success = 1;

                    //form is valid so process the data
                    $model->saveEntity($entity, $form->get('buttons')->get('save')->isClicked());

                    $this->addFlash('mautic.category.notice.updated', array(
                        '%name%' => $entity->getName()
                    ));
                }
            } else {
                $success = 1;

                //unlock the entity
                $model->unlockEntity($entity);
            }
        } else {
            //lock the entity
            $model->lockEntity($entity);
        }

        $closeModal = ($closeModal || $cancelled || ($valid && $form->get('buttons')->get('save')->isClicked()));

        if ($closeModal) {
            if ($inForm) {
                return new JsonResponse(array(
                    'mauticContent' => 'category',
                    'closeModal'    => 1,
                    'inForm'        => 1,
                    'categoryName'  => $entity->getName(),
                    'categoryId'    => $entity->getId()
                ));
            }

            $viewParameters = array(
                'page'   => $session->get('mautic.category.page'),
                'bundle' => $bundle
            );

            return $this->postActionRedirect(array(
                'returnUrl'       => $this->generateUrl('mautic_category_index', $viewParameters),
                'viewParameters'  => $viewParameters,
                'contentTemplate' => 'MauticCategoryBundle:Category:index',
                'passthroughVars' => array(
                    'activeLink'    => '#mautic_' . $bundle . 'category_index',
                    'mauticContent' => 'category',
                    'closeModal'    => 1
                )
            ));
        } else {
            return $this->ajaxAction(array(
                'contentTemplate' => 'MauticCategoryBundle:Category:form.html.php',
                'viewParameters'  => array(
                    'form'           => $form->createView(),
                    'activeCategory' => $entity,
                    'bundle'         => $bundle
                ),
                'passthroughVars' => array(
                    'mauticContent' => 'category',
                    'success'       => $success,
                    'route'         => false
                )
            ));
        }
    }

    /**
     * Clone an entity
     *
     * @param $objectId
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function cloneAction ($bundle, $objectId)
    {
        $model  = $this->factory->getModel('category.category');
        $entity = $model->getEntity($objectId);

        if ($entity != null) {
            if (!$this->factory->getSecurity()->isGranted($bundle . ':categories:create')) {
                return $this->accessDenied();
            }

            $clone = clone $entity;
            $clone->setIsPublished(false);
            $model->saveEntity($clone);
            $objectId = $clone->getId();
        }

        return $this->editAction($bundle, $objectId);
    }

    /**
     * Deletes the entity
     *
     * @param         $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction ($bundle, $objectId)
    {
        $session    = $this->factory->getSession();
        $page       = $session->get('mautic.category.page', 1);
        $viewParams = array(
            'page'   => $page,
            'bundle' => $bundle
        );
        $returnUrl  = $this->generateUrl('mautic_category_index', $viewParams);
        $flashes    = array();

        $postActionVars = array(
            'returnUrl'       => $returnUrl,
            'viewParameters'  => $viewParams,
            'contentTemplate' => 'MauticCategoryBundle:Category:index',
            'passthroughVars' => array(
                'activeLink'    => 'mautic_' . $bundle . 'category_index',
                'mauticContent' => 'category'
            )
        );

        if ($this->request->getMethod() == 'POST') {
            $model  = $this->factory->getModel('category.category');
            $entity = $model->getEntity($objectId);

            if ($entity === null) {
                $flashes[] = array(
                    'type'    => 'error',
                    'msg'     => 'mautic.category.error.notfound',
                    'msgVars' => array('%id%' => $objectId)
                );
            } elseif (!$this->factory->getSecurity()->isGranted($bundle . ':categories:delete')) {
                return $this->accessDenied();
            } elseif ($model->isLocked($entity)) {
                return $this->isLocked($postActionVars, $entity, 'category.category');
            }

            $model->deleteEntity($entity);

            $flashes[] = array(
                'type'    => 'notice',
                'msg'     => 'mautic.core.notice.deleted',
                'msgVars' => array(
                    '%name%' => $entity->getTitle(),
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
     * @param string $bundle
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function batchDeleteAction ($bundle)
    {
        $session    = $this->factory->getSession();
        $page       = $session->get('mautic.category.page', 1);
        $viewParams = array(
            'page'   => $page,
            'bundle' => $bundle
        );
        $returnUrl  = $this->generateUrl('mautic_category_index', $viewParams);
        $flashes    = array();

        $postActionVars = array(
            'returnUrl'       => $returnUrl,
            'viewParameters'  => $viewParams,
            'contentTemplate' => 'MauticCategoryBundle:Category:index',
            'passthroughVars' => array(
                'activeLink'    => 'mautic_' . $bundle . 'category_index',
                'mauticContent' => 'category'
            )
        );

        if ($this->request->getMethod() == 'POST') {
            $model     = $this->factory->getModel('category');
            $ids       = json_decode($this->request->query->get('ids', '{}'));
            $deleteIds = array();

            // Loop over the IDs to perform access checks pre-delete
            foreach ($ids as $objectId) {
                $entity = $model->getEntity($objectId);

                if ($entity === null) {
                    $flashes[] = array(
                        'type'    => 'error',
                        'msg'     => 'mautic.category.error.notfound',
                        'msgVars' => array('%id%' => $objectId)
                    );
                } elseif (!$this->factory->getSecurity()->isGranted($bundle . ':categories:delete')) {
                    $flashes[] = $this->accessDenied(true);
                } elseif ($model->isLocked($entity)) {
                    $flashes[] = $this->isLocked($postActionVars, $entity, 'category', true);
                } else {
                    $deleteIds[] = $objectId;
                }
            }

            // Delete everything we are able to
            if (!empty($deleteIds)) {
                $entities = $model->deleteEntities($deleteIds);

                $flashes[] = array(
                    'type'    => 'notice',
                    'msg'     => 'mautic.category.notice.batch_deleted',
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
