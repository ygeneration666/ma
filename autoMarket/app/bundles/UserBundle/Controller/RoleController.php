<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\UserBundle\Controller;

use Mautic\CoreBundle\Controller\FormController;
use Mautic\UserBundle\Entity as Entity;
use Symfony\Component\HttpKernel\Exception\PreconditionRequiredHttpException;

/**
 * Class RoleController
 */
class RoleController extends FormController
{
    /**
     * Generate's default role list view
     *
     * @param int $page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction ($page = 1)
    {
        if (!$this->factory->getSecurity()->isGranted('user:roles:view')) {
            return $this->accessDenied();
        }

        if ($this->request->getMethod() == 'POST') {
            $this->setListFilters();
        }

        //set limits
        $limit = $this->factory->getSession()->get('mautic.role.limit', $this->factory->getParameter('default_pagelimit'));
        $start = ($page === 1) ? 0 : (($page - 1) * $limit);
        if ($start < 0) {
            $start = 0;
        }

        $orderBy    = $this->factory->getSession()->get('mautic.role.orderby', 'r.name');
        $orderByDir = $this->factory->getSession()->get('mautic.role.orderbydir', 'ASC');
        $filter     = $this->request->get('search', $this->factory->getSession()->get('mautic.role.filter', ''));
        $this->factory->getSession()->set('mautic.role.filter', $filter);
        $tmpl = $this->request->isXmlHttpRequest() ? $this->request->get('tmpl', 'index') : 'index';

        $items = $this->factory->getModel('user.role')->getEntities(
            array(
                'start'      => $start,
                'limit'      => $limit,
                'filter'     => $filter,
                'orderBy'    => $orderBy,
                'orderByDir' => $orderByDir
            ));

        $count = count($items);
        if ($count && $count < ($start + 1)) {
            //the number of entities are now less then the current page so redirect to the last page
            $lastPage = ($count === 1) ? 1 : (ceil($count / $limit)) ?: 1;
            $this->factory->getSession()->set('mautic.role.page', $lastPage);
            $returnUrl = $this->generateUrl('mautic_role_index', array('page' => $lastPage));

            return $this->postActionRedirect(array(
                'returnUrl'       => $returnUrl,
                'viewParameters'  => array(
                    'page' => $lastPage,
                    'tmpl' => $tmpl
                ),
                'contentTemplate' => 'MauticUserBundle:Role:index',
                'passthroughVars' => array(
                    'activeLink'    => '#mautic_role_index',
                    'mauticContent' => 'role'
                )
            ));
        }

        //set what page currently on so that we can return here after form submission/cancellation
        $this->factory->getSession()->set('mautic.role.page', $page);

        //set some permissions
        $permissions = array(
            'create' => $this->factory->getSecurity()->isGranted('user:roles:create'),
            'edit'   => $this->factory->getSecurity()->isGranted('user:roles:edit'),
            'delete' => $this->factory->getSecurity()->isGranted('user:roles:delete'),
        );

        $parameters = array(
            'items'       => $items,
            'searchValue' => $filter,
            'page'        => $page,
            'limit'       => $limit,
            'permissions' => $permissions,
            'tmpl'        => $tmpl
        );

        return $this->delegateView(array(
            'viewParameters'  => $parameters,
            'contentTemplate' => 'MauticUserBundle:Role:list.html.php',
            'passthroughVars' => array(
                'route'         => $this->generateUrl('mautic_role_index', array('page' => $page)),
                'mauticContent' => 'role'
            )
        ));
    }

    /**
     * Generate's new role form and processes post data
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction ()
    {
        if (!$this->factory->getSecurity()->isGranted('user:roles:create')) {
            return $this->accessDenied();
        }

        //retrieve the entity
        $entity = new Entity\Role();
        $model  = $this->factory->getModel('user.role');

        //set the return URL for post actions
        $returnUrl = $this->generateUrl('mautic_role_index');

        //set the page we came from
        $page   = $this->factory->getSession()->get('mautic.role.page', 1);
        $action = $this->generateUrl('mautic_role_action', array('objectAction' => 'new'));

        //get the user form factory
        $permissionsConfig = $this->getPermissionsConfig($entity);
        $form              = $model->createForm($entity, $this->get('form.factory'), $action, array('permissionsConfig' => $permissionsConfig['config']));

        ///Check for a submitted form and process it
        if ($this->request->getMethod() == 'POST') {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    //set the permissions
                    $permissions = $this->request->request->get('role[permissions]', null, true);
                    $model->setRolePermissions($entity, $permissions);

                    //form is valid so process the data
                    $model->saveEntity($entity);

                    $this->addFlash('mautic.core.notice.created', array(
                        '%name%'      => $entity->getName(),
                        '%menu_link%' => 'mautic_role_index',
                        '%url%'       => $this->generateUrl('mautic_role_action', array(
                            'objectAction' => 'edit',
                            'objectId'     => $entity->getId()
                        ))
                    ));
                }
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                return $this->postActionRedirect(array(
                    'returnUrl'       => $returnUrl,
                    'viewParameters'  => array('page' => $page),
                    'contentTemplate' => 'MauticUserBundle:Role:index',
                    'passthroughVars' => array(
                        'activeLink'    => '#mautic_role_index',
                        'mauticContent' => 'role'
                    )
                ));
            } else {
                return $this->editAction($entity->getId(), true);
            }
        }

        return $this->delegateView(array(
            'viewParameters'  => array(
                'form'              => $this->setFormTheme($form, 'MauticUserBundle:Role:form.html.php', 'MauticUserBundle:FormTheme\Role'),
                'permissionsConfig' => $permissionsConfig
            ),
            'contentTemplate' => 'MauticUserBundle:Role:form.html.php',
            'passthroughVars' => array(
                'activeLink'     => '#mautic_role_new',
                'route'          => $this->generateUrl('mautic_role_action', array('objectAction' => 'new')),
                'mauticContent'  => 'role',
                'permissionList' => $permissionsConfig['list']
            )
        ));
    }

    /**
     * Generate's role edit form and processes post data
     *
     * @param int  $objectId
     * @param bool $ignorePost
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction ($objectId, $ignorePost = true)
    {
        if (!$this->factory->getSecurity()->isGranted('user:roles:edit')) {
            return $this->accessDenied();
        }

        /** @var \Mautic\UserBundle\Model\RoleModel $model */
        $model  = $this->factory->getModel('user.role');
        $entity = $model->getEntity($objectId);

        //set the page we came from
        $page = $this->factory->getSession()->get('mautic.role.page', 1);

        //set the return URL
        $returnUrl = $this->generateUrl('mautic_role_index', array('page' => $page));

        $postActionVars = array(
            'returnUrl'       => $returnUrl,
            'viewParameters'  => array('page' => $page),
            'contentTemplate' => 'MauticUserBundle:Role:index',
            'passthroughVars' => array(
                'activeLink'    => '#mautic_role_index',
                'mauticContent' => 'role'
            )
        );

        //user not found
        if ($entity === null) {
            return $this->postActionRedirect(
                array_merge($postActionVars, array(
                    'flashes' => array(
                        array(
                            'type'    => 'error',
                            'msg'     => 'mautic.user.role.error.notfound',
                            'msgVars' => array('%id' => $objectId)
                        )
                    )
                ))
            );
        } elseif ($model->isLocked($entity)) {
            //deny access if the entity is locked
            return $this->isLocked($postActionVars, $entity, 'user.role');
        }

        $permissionsConfig = $this->getPermissionsConfig($entity);
        $action            = $this->generateUrl('mautic_role_action', array('objectAction' => 'edit', 'objectId' => $objectId));
        $form              = $model->createForm($entity, $this->get('form.factory'), $action, array('permissionsConfig' => $permissionsConfig['config']));

        ///Check for a submitted form and process it
        if (!$ignorePost && $this->request->getMethod() == 'POST') {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    //set the permissions
                    $permissions = $this->request->request->get('role[permissions]', null, true);
                    $model->setRolePermissions($entity, $permissions);

                    //form is valid so process the data
                    $model->saveEntity($entity, $form->get('buttons')->get('save')->isClicked());

                    $this->addFlash('mautic.core.notice.updated', array(
                        '%name%'      => $entity->getName(),
                        '%menu_link%' => 'mautic_role_index',
                        '%url%'       => $this->generateUrl('mautic_role_action', array(
                            'objectAction' => 'edit',
                            'objectId'     => $entity->getId()
                        ))
                    ));
                }
            } else {
                //unlock the entity
                $model->unlockEntity($entity);
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                return $this->postActionRedirect($postActionVars);
            } else {
                //the form has to be rebuilt because the permissions were updated
                $permissionsConfig = $this->getPermissionsConfig($entity);
                $form              = $model->createForm($entity, $this->get('form.factory'), $action, array('permissionsConfig' => $permissionsConfig['config']));
            }
        } else {
            //lock the entity
            $model->lockEntity($entity);
        }

        return $this->delegateView(array(
            'viewParameters'  => array(
                'form'              => $this->setFormTheme($form, 'MauticUserBundle:Role:form.html.php', 'MauticUserBundle:FormTheme\Role'),
                'permissionsConfig' => $permissionsConfig
            ),
            'contentTemplate' => 'MauticUserBundle:Role:form.html.php',
            'passthroughVars' => array(
                'activeLink'     => '#mautic_role_index',
                'route'          => $action,
                'mauticContent'  => 'role',
                'permissionList' => $permissionsConfig['list']
            )
        ));
    }

    /**
     * @param Entity\Role $role
     *
     * @return array
     */
    private function getPermissionsConfig (Entity\Role $role)
    {
        $permissionObjects = $this->factory->getSecurity()->getPermissionObjects();
        $translator        = $this->factory->getTranslator();

        $permissionsArray = ($role->getId()) ?
            $this->factory->getEntityManager()->getRepository('MauticUserBundle:Permission')->getPermissionsByRole($role, true) :
            array();

        $permissions     = array();
        $permissionsList = array();
        /** @var \Mautic\CoreBundle\Security\Permissions\AbstractPermissions $object */
        foreach ($permissionObjects as $object) {
            if (!is_object($object)) {
                continue;
            }

            if ($object->isEnabled()) {
                $bundle = $object->getName();
                $label  = $translator->trans("mautic.{$bundle}.permissions.header");

                //convert the permission bits from the db into readable names
                $data = $object->convertBitsToPermissionNames($permissionsArray);

                //get the ratio of granted/total
                list($granted, $total) = $object->getPermissionRatio($data);

                $permissions[$bundle] = array(
                    'label'            => $label,
                    'permissionObject' => $object,
                    'ratio'            => array($granted, $total),
                    'data'             => $data
                );

                $perms = $object->getPermissions();
                foreach ($perms as $level => $perm) {
                    $levelPerms = array_keys($perm);
                    $object->parseForJavascript($levelPerms);
                    $permissionsList[$bundle][$level] = $levelPerms;
                }
            }
        }

        //order permissions by label
        uasort($permissions, function ($a, $b) {
            return strnatcmp($a['label'], $b['label']);
        });

        return array('config' => $permissions, 'list' => $permissionsList);
    }

    /**
     * Delete's a role
     *
     * @param int $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction ($objectId)
    {
        if (!$this->factory->getSecurity()->isGranted('user:roles:delete')) {
            return $this->accessDenied();
        }

        $page           = $this->factory->getSession()->get('mautic.role.page', 1);
        $returnUrl      = $this->generateUrl('mautic_role_index', array('page' => $page));
        $success        = 0;
        $flashes        = array();
        $postActionVars = array(
            'returnUrl'       => $returnUrl,
            'viewParameters'  => array('page' => $page),
            'contentTemplate' => 'MauticUserBundle:Role:index',
            'passthroughVars' => array(
                'activeLink'    => '#mautic_role_index',
                'success'       => $success,
                'mauticContent' => 'role'
            )
        );

        if ($this->request->getMethod() == 'POST') {
            try {
                $model  = $this->factory->getModel('user.role');
                $entity = $model->getEntity($objectId);

                if ($entity === null) {
                    $flashes[] = array(
                        'type'    => 'error',
                        'msg'     => 'mautic.user.role.error.notfound',
                        'msgVars' => array('%id%' => $objectId)
                    );
                } elseif ($model->isLocked($entity)) {
                    return $this->isLocked($postActionVars, $entity, 'user.role');
                } else {
                    $model->deleteEntity($entity);
                    $name      = $entity->getName();
                    $flashes[] = array(
                        'type'    => 'notice',
                        'msg'     => 'mautic.core.notice.deleted',
                        'msgVars' => array(
                            '%name%' => $name,
                            '%id%'   => $objectId
                        )
                    );
                }
            } catch (PreconditionRequiredHttpException $e) {
                $flashes[] = array(
                    'type' => 'error',
                    'msg'  => $e->getMessage()
                );
            }

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
        $page        = $this->factory->getSession()->get('mautic.role.page', 1);
        $returnUrl   = $this->generateUrl('mautic_role_index', array('page' => $page));
        $flashes     = array();

        $postActionVars = array(
            'returnUrl'       => $returnUrl,
            'viewParameters'  => array('page' => $page),
            'contentTemplate' => 'MauticUserBundle:Role:index',
            'passthroughVars' => array(
                'activeLink'    => '#mautic_role_index',
                'mauticContent' => 'role'
            )
        );

        if ($this->request->getMethod() == 'POST') {
            $model     = $this->factory->getModel('user.role');
            $ids       = json_decode($this->request->query->get('ids', ''));
            $deleteIds = array();
            $currentUser    = $this->factory->getUser();

            // Loop over the IDs to perform access checks pre-delete
            foreach ($ids as $objectId) {
                $entity = $model->getEntity($objectId);
                $users  = $this->factory->getEntityManager()->getRepository('MauticUserBundle:User')->findByRole($entity);

                if ($entity === null) {
                    $flashes[] = array(
                        'type'    => 'error',
                        'msg'     => 'mautic.user.role.error.notfound',
                        'msgVars' => array('%id%' => $objectId)
                    );
                } elseif (count($users)) {
                    $flashes[] = array(
                        'type'    => 'error',
                        'msg'     => 'mautic.user.role.error.deletenotallowed',
                        'msgVars' => array('%name%' => $entity->getName())
                    );
                } elseif(!$this->factory->getSecurity()->isGranted('user:roles:delete')) {
                    $flashes[] = $this->accessDenied(true);
                } elseif ($model->isLocked($entity)) {
                    $flashes[] = $this->isLocked($postActionVars, $entity, 'user.role', true);
                } else {
                    $deleteIds[] = $objectId;
                }
            }

            // Delete everything we are able to
            if (!empty($deleteIds)) {
                $entities = $model->deleteEntities($deleteIds);

                $flashes[] = array(
                    'type' => 'notice',
                    'msg'  => 'mautic.user.role.notice.batch_deleted',
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
