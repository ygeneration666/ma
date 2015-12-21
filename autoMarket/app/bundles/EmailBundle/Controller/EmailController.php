<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\EmailBundle\Controller;

use Mautic\CoreBundle\Controller\FormController;
use Mautic\CoreBundle\Helper\BuilderTokenHelper;
use Mautic\CoreBundle\Helper\EmojiHelper;
use Mautic\CoreBundle\Helper\InputHelper;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailSendEvent;
use Mautic\EmailBundle\Entity\Email;
use Symfony\Component\HttpFoundation\Response;

class EmailController extends FormController
{

    /**
     * @param int $page
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($page = 1)
    {
        $model = $this->factory->getModel('email');

        //set some permissions
        $permissions = $this->factory->getSecurity()->isGranted(
            array(
                'email:emails:viewown',
                'email:emails:viewother',
                'email:emails:create',
                'email:emails:editown',
                'email:emails:editother',
                'email:emails:deleteown',
                'email:emails:deleteother',
                'email:emails:publishown',
                'email:emails:publishother'
            ),
            "RETURN_ARRAY"
        );

        if (!$permissions['email:emails:viewown'] && !$permissions['email:emails:viewother']) {
            return $this->accessDenied();
        }

        if ($this->request->getMethod() == 'POST') {
            $this->setListFilters();
        }

        $session = $this->factory->getSession();

        $listFilters = array(
            'filters'      => array(
                'multiple' => true
            ),
        );

        // Reset available groups
        $listFilters['filters']['groups'] = array();

        //set limits
        $limit = $session->get('mautic.email.limit', $this->factory->getParameter('default_pagelimit'));
        $start = ($page === 1) ? 0 : (($page - 1) * $limit);
        if ($start < 0) {
            $start = 0;
        }

        $search  = $this->request->get('search', $session->get('mautic.email.filter', ''));
        $session->set('mautic.email.filter', $search);

        $filter = array(
            'string' => $search,
            'force'  => array(
                array('column' => 'e.variantParent', 'expr' => 'isNull')
            )
        );
        if (!$permissions['email:emails:viewother']) {
            $filter['force'][] =
                array('column' => 'e.createdBy', 'expr' => 'eq', 'value' => $this->factory->getUser()->getId());
        }

        //retrieve a list of categories
        $listFilters['filters']['groups']['mautic.core.filter.categories'] = array(
            'options'  => $this->factory->getModel('category')->getLookupResults('email', '', 0),
            'prefix'   => 'category'
        );

        //retrieve a list of Lead Lists
        $listFilters['filters']['groups']['mautic.core.filter.lists'] = array(
            'options'  => $this->factory->getModel('lead.list')->getUserLists(),
            'prefix'   => 'list'
        );

        //retrieve a list of themes
        $listFilters['filters']['groups']['mautic.core.filter.themes'] = array(
            'options'  => $this->factory->getInstalledThemes('email'),
            'prefix'   => 'theme'
        );

        $currentFilters = $session->get('mautic.email.list_filters', array());
        $updatedFilters = $this->request->get('filters', false);

        if ($updatedFilters) {
            // Filters have been updated

            // Parse the selected values
            $newFilters     = array();
            $updatedFilters = json_decode($updatedFilters, true);

            if ($updatedFilters) {
                foreach ($updatedFilters as $updatedFilter) {
                    list($clmn, $fltr) = explode(':', $updatedFilter);

                    $newFilters[$clmn][] = $fltr;
                }

                $currentFilters = $newFilters;
            } else {
                $currentFilters = array();
            }
        }
        $session->set('mautic.email.list_filters', $currentFilters);

        if (!empty($currentFilters)) {
            $listIds = $catIds = $templates = array();
            foreach ($currentFilters as $type => $typeFilters) {
                switch ($type) {
                    case 'list':
                        $key = 'lists';
                        break;
                    case 'category':
                        $key = 'categories';
                        break;
                    case 'theme':
                        $key = 'themes';
                        break;
                }

                $listFilters['filters']['groups']['mautic.core.filter.' . $key]['values'] = $typeFilters;

                foreach ($typeFilters as $fltr) {
                    switch ($type) {
                        case 'list':
                            $listIds[] = (int) $fltr;
                            break;
                        case 'category':
                            $catIds[] = (int) $fltr;
                            break;
                        case 'theme':
                            $templates[] = $fltr;
                            break;
                    }
                }
            }

            if (!empty($listIds)) {
                $filter['force'][] = array('column' => 'l.id', 'expr' => 'in', 'value' => $listIds);
            }

            if (!empty($catIds)) {
                $filter['force'][] = array('column' => 'c.id', 'expr' => 'in', 'value' => $catIds);
            }

            if (!empty($templates)) {
                $filter['force'][] = array('column' => 'e.template', 'expr' => 'in', 'value' => $templates);
            }
        }

        $orderBy    = $session->get('mautic.email.orderby', 'e.subject');
        $orderByDir = $session->get('mautic.email.orderbydir', 'DESC');

        $emails = $model->getEntities(
            array(
                'start'      => $start,
                'limit'      => $limit,
                'filter'     => $filter,
                'orderBy'    => $orderBy,
                'orderByDir' => $orderByDir
            )
        );

        $count = count($emails);
        if ($count && $count < ($start + 1)) {
            //the number of entities are now less then the current page so redirect to the last page
            if ($count === 1) {
                $lastPage = 1;
            } else {
                $lastPage = (floor($count / $limit)) ?: 1;
            }

            $session->set('mautic.email.page', $lastPage);
            $returnUrl = $this->generateUrl('mautic_email_index', array('page' => $lastPage));

            return $this->postActionRedirect(
                array(
                    'returnUrl'       => $returnUrl,
                    'viewParameters'  => array('page' => $lastPage),
                    'contentTemplate' => 'MauticEmailBundle:Email:index',
                    'passthroughVars' => array(
                        'activeLink'    => '#mautic_email_index',
                        'mauticContent' => 'email'
                    )
                )
            );
        }
        $session->set('mautic.email.page', $page);

        return $this->delegateView(
            array(
                'viewParameters'  =>  array(
                    'searchValue' => $search,
                    'filters'     => $listFilters,
                    'items'       => $emails,
                    'totalItems'  => $count,
                    'page'        => $page,
                    'limit'       => $limit,
                    'tmpl'        => $this->request->get('tmpl', 'index'),
                    'permissions' => $permissions,
                    'model'       => $model,
                    'security'    => $this->factory->getSecurity(),
                ),
                'contentTemplate' => 'MauticEmailBundle:Email:list.html.php',
                'passthroughVars' => array(
                    'activeLink'    => '#mautic_email_index',
                    'mauticContent' => 'email',
                    'route'         => $this->generateUrl('mautic_email_index', array('page' => $page))
                )
            )
        );
    }

    /**
     * Loads a specific form into the detailed panel
     *
     * @param $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($objectId)
    {
        /** @var \Mautic\EmailBundle\Model\EmailModel $model */
        $model    = $this->factory->getModel('email');
        $security = $this->factory->getSecurity();

        /** @var \Mautic\EmailBundle\Entity\Email $email */
        $email = $model->getEntity($objectId);
        //set the page we came from
        $page = $this->factory->getSession()->get('mautic.email.page', 1);

        if ($email === null) {
            //set the return URL
            $returnUrl = $this->generateUrl('mautic_email_index', array('page' => $page));

            return $this->postActionRedirect(
                array(
                    'returnUrl'       => $returnUrl,
                    'viewParameters'  => array('page' => $page),
                    'contentTemplate' => 'MauticEmailBundle:Email:index',
                    'passthroughVars' => array(
                        'activeLink'    => '#mautic_email_index',
                        'mauticContent' => 'email'
                    ),
                    'flashes'         => array(
                        array(
                            'type'    => 'error',
                            'msg'     => 'mautic.email.error.notfound',
                            'msgVars' => array('%id%' => $objectId)
                        )
                    )
                )
            );
        } elseif (!$this->factory->getSecurity()->hasEntityAccess(
            'email:emails:viewown',
            'email:emails:viewother',
            $email->getCreatedBy()
        )
        ) {
            return $this->accessDenied();
        }

        //get A/B test information
        list($parent, $children) = $model->getVariants($email);
        $properties   = array();
        $variantError = false;
        $weight       = 0;
        if (count($children)) {
            foreach ($children as $c) {
                $variantSettings = $c->getVariantSettings();

                if (is_array($variantSettings) && isset($variantSettings['winnerCriteria'])) {
                    if ($c->isPublished()) {
                        if (!isset($lastCriteria)) {
                            $lastCriteria = $variantSettings['winnerCriteria'];
                        }

                        //make sure all the variants are configured with the same criteria
                        if ($lastCriteria != $variantSettings['winnerCriteria']) {
                            $variantError = true;
                        }

                        $weight += $variantSettings['weight'];
                    }
                } else {
                    $variantSettings['winnerCriteria'] = '';
                    $variantSettings['weight']         = 0;
                }

                $properties[$c->getId()] = $variantSettings;
            }

            $properties[$parent->getId()]['weight']         = 100 - $weight;
            $properties[$parent->getId()]['winnerCriteria'] = '';
        }

        $abTestResults = array();
        $criteria      = $model->getBuilderComponents($email, 'abTestWinnerCriteria');
        if (!empty($lastCriteria) && empty($variantError)) {
            if (isset($criteria['criteria'][$lastCriteria])) {
                $testSettings = $criteria['criteria'][$lastCriteria];

                $args = array(
                    'factory'    => $this->factory,
                    'email'      => $email,
                    'parent'     => $parent,
                    'children'   => $children,
                    'properties' => $properties
                );

                //execute the callback
                if (is_callable($testSettings['callback'])) {
                    if (is_array($testSettings['callback'])) {
                        $reflection = new \ReflectionMethod($testSettings['callback'][0], $testSettings['callback'][1]);
                    } elseif (strpos($testSettings['callback'], '::') !== false) {
                        $parts      = explode('::', $testSettings['callback']);
                        $reflection = new \ReflectionMethod($parts[0], $parts[1]);
                    } else {
                        $reflection = new \ReflectionMethod(null, $testSettings['callback']);
                    }

                    $pass = array();
                    foreach ($reflection->getParameters() as $param) {
                        if (isset($args[$param->getName()])) {
                            $pass[] = $args[$param->getName()];
                        } else {
                            $pass[] = null;
                        }
                    }
                    $abTestResults = $reflection->invokeArgs($this, $pass);
                }
            }
        }

        // Prepare stats for bargraph
        $variant = ($parent && $parent === $email);
        $stats   = ($email->getEmailType() == 'template') ? $model->getEmailGeneralStats($email, $variant) : $model->getEmailListStats($email, $variant);

        // Audit Log
        $logs = $this->factory->getModel('core.auditLog')->getLogForObject('email', $email->getId(), $email->getDateAdded());

        // Get click through stats
        $trackableLinks = $model->getEmailClickStats($email->getId());

        return $this->delegateView(
            array(
                'returnUrl'       => $this->generateUrl(
                    'mautic_email_action',
                    array(
                        'objectAction' => 'view',
                        'objectId'     => $email->getId()
                    )
                ),
                'viewParameters'  => array(
                    'email'          => $email,
                    'stats'          => $stats,
                    'trackableLinks' => $trackableLinks,
                    'pending'        => $model->getPendingLeads($email, null, true),
                    'logs'           => $logs,
                    'variants'       => array(
                        'parent'     => $parent,
                        'children'   => $children,
                        'properties' => $properties,
                        'criteria'   => $criteria['criteria']
                    ),
                    'permissions'    => $security->isGranted(
                        array(
                            'email:emails:viewown',
                            'email:emails:viewother',
                            'email:emails:create',
                            'email:emails:editown',
                            'email:emails:editother',
                            'email:emails:deleteown',
                            'email:emails:deleteother',
                            'email:emails:publishown',
                            'email:emails:publishother'
                        ),
                        "RETURN_ARRAY"
                    ),
                    'abTestResults'  => $abTestResults,
                    'security'       => $security,
                    'previewUrl'     => $this->generateUrl(
                        'mautic_email_preview',
                        array('objectId' => $email->getId()),
                        true
                    )
                ),
                'contentTemplate' => 'MauticEmailBundle:Email:details.html.php',
                'passthroughVars' => array(
                    'activeLink'    => '#mautic_email_index',
                    'mauticContent' => 'email'
                )
            )
        );
    }

    /**
     * Generates new form and processes post data
     *
     * @param  \Mautic\EmailBundle\Entity\Email $entity
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction($entity = null)
    {
        $model   = $this->factory->getModel('email');

        if (!($entity instanceof Email)) {
            /** @var \Mautic\EmailBundle\Entity\Email $entity */
            $entity  = $model->getEntity();
        }

        $method  = $this->request->getMethod();
        $session = $this->factory->getSession();
        if (!$this->factory->getSecurity()->isGranted('email:emails:create')) {
            return $this->accessDenied();
        }

        //set the page we came from
        $page   = $session->get('mautic.email.page', 1);
        $action = $this->generateUrl('mautic_email_action', array('objectAction' => 'new'));

        $updateSelect = ($method == 'POST')
            ? $this->request->request->get('emailform[updateSelect]', false, true)
            : $this->request->get(
                'updateSelect',
                false
            );

        if ($updateSelect) {
            // Force type to template
            $entity->setEmailType('template');
        }

        //create the form
        $form = $model->createForm($entity, $this->get('form.factory'), $action, array('update_select' => $updateSelect));

        ///Check for a submitted form and process it
        if ($method == 'POST') {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    $session     = $this->factory->getSession();
                    $contentName = 'mautic.emailbuilder.'.$entity->getSessionId().'.content';

                    $template = $entity->getTemplate();
                    if (!empty($template)) {
                        $content = $session->get($contentName, array());
                        $entity->setCustomHtml(null);
                    } else {
                        $content = $entity->getCustomHtml();
                        $entity->setContent(array());
                    }

                    // Parse visual placeholders into tokens
                    BuilderTokenHelper::replaceVisualPlaceholdersWithTokens($content);

                    if (!empty($template)) {
                        $entity->setContent($content);
                    } else {
                        $entity->setCustomHtml($content);
                    }

                    //form is valid so process the data
                    $model->saveEntity($entity);

                    //clear the session
                    $session->remove($contentName);

                    $this->addFlash(
                        'mautic.core.notice.created',
                        array(
                            '%name%'      => $entity->getName(),
                            '%menu_link%' => 'mautic_email_index',
                            '%url%'       => $this->generateUrl(
                                'mautic_email_action',
                                array(
                                    'objectAction' => 'edit',
                                    'objectId'     => $entity->getId()
                                )
                            )
                        )
                    );

                    if ($form->get('buttons')->get('save')->isClicked()) {
                        $viewParameters = array(
                            'objectAction' => 'view',
                            'objectId'     => $entity->getId()
                        );
                        $returnUrl      = $this->generateUrl('mautic_email_action', $viewParameters);
                        $template       = 'MauticEmailBundle:Email:view';
                    } else {
                        //return edit view so that all the session stuff is loaded
                        return $this->editAction($entity->getId(), true);
                    }
                }
            } else {
                $viewParameters = array('page' => $page);
                $returnUrl      = $this->generateUrl('mautic_email_index', $viewParameters);
                $template       = 'MauticEmailBundle:Email:index';
                //clear any modified content
                $session->remove('mautic.emailbuilder.'.$entity->getSessionId().'.content');
            }

            $passthrough = array(
                'activeLink'    => 'mautic_email_index',
                'mauticContent' => 'email'
            );

            // Check to see if this is a popup
            if (isset($form['updateSelect'])) {
                $passthrough = array_merge(
                    $passthrough,
                    array(
                        'updateSelect' => $form['updateSelect']->getData(),
                        'emailId'      => $entity->getId(),
                        'emailSubject' => $entity->getSubject(),
                        'emailName'    => $entity->getName(),
                        'emailLang'    => $entity->getLanguage()
                    )
                );
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                return $this->postActionRedirect(
                    array(
                        'returnUrl'       => $returnUrl,
                        'viewParameters'  => $viewParameters,
                        'contentTemplate' => $template,
                        'passthroughVars' => $passthrough
                    )
                );
            }
        }

        return $this->delegateView(
            array(
                'viewParameters'  => array(
                    'form'                => $this->setFormTheme($form, 'MauticEmailBundle:Email:form.html.php', 'MauticEmailBundle:FormTheme\Email'),
                    'tokens'              => $model->getBuilderComponents($entity, 'tokenSections'),
                    'email'               => $entity
                ),
                'contentTemplate' => 'MauticEmailBundle:Email:form.html.php',
                'passthroughVars' => array(
                    'activeLink'    => '#mautic_email_index',
                    'mauticContent' => 'email',
                    'updateSelect'  => InputHelper::clean($this->request->query->get('updateSelect')),
                    'route'         => $this->generateUrl(
                        'mautic_email_action',
                        array(
                            'objectAction' => 'new'
                        )
                    )
                )
            )
        );
    }

    /**
     * @param      $objectId
     * @param bool $ignorePost
     * @param bool $forceTypeSelection
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction($objectId, $ignorePost = false, $forceTypeSelection = false)
    {
        /** @var \Mautic\EmailBundle\Model\EmailModel $model */
        $model = $this->factory->getModel('email');

        $entity  = $model->getEntity($objectId);
        $session = $this->factory->getSession();
        $page    = $this->factory->getSession()->get('mautic.email.page', 1);

        //set the return URL
        $returnUrl = $this->generateUrl('mautic_email_index', array('page' => $page));

        $postActionVars = array(
            'returnUrl'       => $returnUrl,
            'viewParameters'  => array('page' => $page),
            'contentTemplate' => 'MauticEmailBundle:Email:index',
            'passthroughVars' => array(
                'activeLink'    => 'mautic_email_index',
                'mauticContent' => 'email'
            )
        );

        //not found
        if ($entity === null) {
            return $this->postActionRedirect(
                array_merge(
                    $postActionVars,
                    array(
                        'flashes' => array(
                            array(
                                'type'    => 'error',
                                'msg'     => 'mautic.email.error.notfound',
                                'msgVars' => array('%id%' => $objectId)
                            )
                        )
                    )
                )
            );
        } elseif (!$this->factory->getSecurity()->hasEntityAccess(
            'email:emails:viewown',
            'email:emails:viewother',
            $entity->getCreatedBy()
        )
        ) {
            return $this->accessDenied();
        } elseif ($entity->getEmailType() == 'list' && $entity->getSentCount()) {
            return $this->postActionRedirect(
                array_merge(
                    $postActionVars,
                    array(
                        'flashes' => array(
                            array(
                                'type'    => 'error',
                                'msg'     => 'mautic.email.error.list_type.sent',
                                'msgVars' => array('%name%' => $entity->getName())
                            )
                        )
                    )
                )
            );
        } elseif ($model->isLocked($entity)) {
            //deny access if the entity is locked
            return $this->isLocked($postActionVars, $entity, 'email');
        }

        //Create the form
        $action = $this->generateUrl('mautic_email_action', array('objectAction' => 'edit', 'objectId' => $objectId));
        $form   = $model->createForm($entity, $this->get('form.factory'), $action);

        ///Check for a submitted form and process it
        if (!$ignorePost && $this->request->getMethod() == 'POST') {
            $valid = false;
            if (!$cancelled = $this->isFormCancelled($form)) {
                if ($valid = $this->isFormValid($form)) {
                    $contentName = 'mautic.emailbuilder.'.$entity->getSessionId().'.content';
                    $template    = $entity->getTemplate();
                    if (!empty($template)) {
                        $existingContent = $entity->getContent();
                        $newContent      = $session->get($contentName, array());
                        $viewContent     = array_merge($existingContent, $newContent);

                        $entity->setCustomHtml(null);
                    } else {
                        $entity->setContent(array());

                        $viewContent = $entity->getCustomHtml();
                    }

                    // Copy model content then parse from visual to tokens
                    $modelContent = $viewContent;
                    BuilderTokenHelper::replaceVisualPlaceholdersWithTokens($modelContent);

                    if (!empty($template)) {
                        $entity->setContent($modelContent);
                    } else {
                        $entity->setCustomHtml($modelContent);
                    }

                    //form is valid so process the data
                    $model->saveEntity($entity, $form->get('buttons')->get('save')->isClicked());

                    //clear the session
                    $session->remove($contentName);

                    $this->addFlash(
                        'mautic.core.notice.updated',
                        array(
                            '%name%'      => $entity->getName(),
                            '%menu_link%' => 'mautic_email_index',
                            '%url%'       => $this->generateUrl(
                                'mautic_email_action',
                                array(
                                    'objectAction' => 'edit',
                                    'objectId'     => $entity->getId()
                                )
                            )
                        ),
                        'warning'
                    );
                }
            } else {
                //clear any modified content
                $session->remove('mautic.emailbuilder.'.$objectId.'.content');
                //unlock the entity
                $model->unlockEntity($entity);
            }

            if ($cancelled || ($valid && $form->get('buttons')->get('save')->isClicked())) {
                $viewParameters = array(
                    'objectAction' => 'view',
                    'objectId'     => $entity->getId()
                );

                return $this->postActionRedirect(
                    array_merge(
                        $postActionVars,
                        array(
                            'returnUrl'       => $this->generateUrl('mautic_email_action', $viewParameters),
                            'viewParameters'  => $viewParameters,
                            'contentTemplate' => 'MauticEmailBundle:Email:view'
                        )
                    )
                );
            }
        } else {
            //lock the entity
            $model->lockEntity($entity);

            //clear any modified content
            $session->remove('mautic.emailbuilder.'.$objectId.'.content');

            // Parse tokens into view data
            $tokens = $model->getBuilderComponents($entity, array('tokens', 'visualTokens', 'tokenSections'));

            // Set to view content
            $template = $entity->getTemplate();
            if (empty($template)) {
                $content = $entity->getCustomHtml();
                BuilderTokenHelper::replaceTokensWithVisualPlaceholders($tokens, $content);
                $form['customHtml']->setData($content);
            }
        }

        $assets         = $form['assetAttachments']->getData();
        $attachmentSize = $this->factory->getModel('asset')->getTotalFilesize($assets);

        return $this->delegateView(
            array(
                'viewParameters'  => array(
                    'form'               => $this->setFormTheme($form, 'MauticEmailBundle:Email:form.html.php', 'MauticEmailBundle:FormTheme\Email'),
                    'tokens'             => (!empty($tokens)) ? $tokens['tokenSections'] : $model->getBuilderComponents($entity, 'tokenSections'),
                    'email'              => $entity,
                    'forceTypeSelection' => $forceTypeSelection,
                    'attachmentSize'     => $attachmentSize
                ),
                'contentTemplate' => 'MauticEmailBundle:Email:form.html.php',
                'passthroughVars' => array(
                    'activeLink'    => '#mautic_email_index',
                    'mauticContent' => 'email',
                    'route'         => $this->generateUrl(
                        'mautic_email_action',
                        array(
                            'objectAction' => 'edit',
                            'objectId'     => $entity->getId()
                        )
                    )
                )
            )
        );
    }

    /**
     * Clone an entity
     *
     * @param $objectId
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function cloneAction($objectId)
    {
        $model  = $this->factory->getModel('email');
        $clone = $model->getEntity($objectId);

        if ($clone != null) {
            if (!$this->factory->getSecurity()->isGranted('email:emails:create')
                || !$this->factory->getSecurity()->hasEntityAccess(
                    'email:emails:viewown',
                    'email:emails:viewother',
                    $clone->getCreatedBy()
                )
            ) {
                return $this->accessDenied();
            }

            /** @var \Mautic\EmailBundle\Entity\Email $clone */
            $clone = clone $clone;
            $clone->clearStats();
            $clone->setSentCount(0);
            $clone->setReadCount(0);
            $clone->setRevision(0);
            $clone->setVariantSentCount(0);
            $clone->setVariantStartDate(null);
        }

        return $this->newAction($clone);
    }

    /**
     * Deletes the entity
     *
     * @param         $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($objectId)
    {
        $page      = $this->factory->getSession()->get('mautic.email.page', 1);
        $returnUrl = $this->generateUrl('mautic_email_index', array('page' => $page));
        $flashes   = array();

        $postActionVars = array(
            'returnUrl'       => $returnUrl,
            'viewParameters'  => array('page' => $page),
            'contentTemplate' => 'MauticEmailBundle:Email:index',
            'passthroughVars' => array(
                'activeLink'    => 'mautic_email_index',
                'mauticContent' => 'email'
            )
        );

        if ($this->request->getMethod() == 'POST') {
            $model  = $this->factory->getModel('email');
            $entity = $model->getEntity($objectId);

            if ($entity === null) {
                $flashes[] = array(
                    'type'    => 'error',
                    'msg'     => 'mautic.email.error.notfound',
                    'msgVars' => array('%id%' => $objectId)
                );
            } elseif (!$this->factory->getSecurity()->hasEntityAccess(
                'email:emails:deleteown',
                'email:emails:deleteother',
                $entity->getCreatedBy()
            )
            ) {
                return $this->accessDenied();
            } elseif ($model->isLocked($entity)) {
                return $this->isLocked($postActionVars, $entity, 'email');
            }

            $model->deleteEntity($entity);

            $flashes[] = array(
                'type'    => 'notice',
                'msg'     => 'mautic.core.notice.deleted',
                'msgVars' => array(
                    '%name%' => $entity->getName(),
                    '%id%'   => $objectId
                )
            );
        } //else don't do anything

        return $this->postActionRedirect(
            array_merge(
                $postActionVars,
                array(
                    'flashes' => $flashes
                )
            )
        );
    }

    /**
     * Activate the builder
     *
     * @param $objectId
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     * @throws \Mautic\CoreBundle\Exception\FileNotFoundException
     */
    public function builderAction($objectId)
    {
        /** @var \Mautic\EmailBundle\Model\EmailModel $model */
        $model = $this->factory->getModel('email');

        //permission check
        if (strpos($objectId, 'new') !== false) {
            $isNew = true;
            if (!$this->factory->getSecurity()->isGranted('email:emails:create')) {
                return $this->accessDenied();
            }
            $entity = $model->getEntity();
            $entity->setSessionId($objectId);
        } else {
            $isNew  = false;
            $entity = $model->getEntity($objectId);
            if ($entity == null
                || !$this->factory->getSecurity()->hasEntityAccess(
                    'email:emails:viewown',
                    'email:emails:viewother',
                    $entity->getCreatedBy()
                )
            ) {
                return $this->accessDenied();
            }
        }

        $template = InputHelper::clean($this->request->query->get('template'));
        $slots    = $this->factory->getTheme($template)->getSlots('email');

        //merge any existing changes
        $newContent = $this->factory->getSession()->get('mautic.emailbuilder.'.$objectId.'.content', array());
        $content    = $entity->getContent();

        $tokens = $model->getBuilderComponents($entity, array('tokens', 'visualTokens'));
        BuilderTokenHelper::replaceTokensWithVisualPlaceholders($tokens, $content);

        if (is_array($newContent)) {
            $content = array_merge($content, $newContent);
        }

        // Replace short codes to emoji
        $content = EmojiHelper::toEmoji($content, 'short');

        return $this->render(
            'MauticEmailBundle::builder.html.php',
            array(
                'isNew'    => $isNew,
                'slots'    => $slots,
                'content'  => $content,
                'email'    => $entity,
                'template' => $template,
                'basePath' => $this->request->getBasePath()
            )
        );
    }

    /**
     * Create an AB test
     *
     * @param $objectId
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function abtestAction($objectId)
    {
        $model  = $this->factory->getModel('email');
        $entity = $model->getEntity($objectId);

        if ($entity != null) {
            $parent = $entity->getVariantParent();

            if ($parent || !$this->factory->getSecurity()->isGranted('email:emails:create')
                || !$this->factory->getSecurity()->hasEntityAccess(
                    'email:emails:viewown',
                    'email:emails:viewother',
                    $entity->getCreatedBy()
                )
            ) {
                return $this->accessDenied();
            }

            $clone = clone $entity;

            //reset
            $clone->clearStats();
            $clone->setSentCount(0);
            $clone->setRevision(0);
            $clone->setVariantSentCount(0);
            $clone->setVariantStartDate(null);
            $clone->setIsPublished(false);
            $clone->setVariantParent($entity);

            $model->saveEntity($clone);
            $objectId = $clone->getId();
        }

        return $this->editAction($objectId, true);
    }

    /**
     * Make the variant the main
     *
     * @param $objectId
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function winnerAction($objectId)
    {
        //todo - add confirmation to button click
        $page      = $this->factory->getSession()->get('mautic.email', 1);
        $returnUrl = $this->generateUrl('mautic_email_index', array('page' => $page));
        $flashes   = array();

        $postActionVars = array(
            'returnUrl'       => $returnUrl,
            'viewParameters'  => array('page' => $page),
            'contentTemplate' => 'MauticEmailBundle:Page:index',
            'passthroughVars' => array(
                'activeLink'    => 'mautic_email_index',
                'mauticContent' => 'page'
            )
        );

        if ($this->request->getMethod() == 'POST') {
            $model  = $this->factory->getModel('email');
            $entity = $model->getEntity($objectId);

            if ($entity === null) {
                $flashes[] = array(
                    'type'    => 'error',
                    'msg'     => 'mautic.email.error.notfound',
                    'msgVars' => array('%id%' => $objectId)
                );
            } elseif (!$this->factory->getSecurity()->hasEntityAccess(
                'email:emails:editown',
                'email:emails:editother',
                $entity->getCreatedBy()
            )
            ) {
                return $this->accessDenied();
            } elseif ($model->isLocked($entity)) {
                return $this->isLocked($postActionVars, $entity, 'email');
            }

            $model->convertVariant($entity);

            $flashes[] = array(
                'type'    => 'notice',
                'msg'     => 'mautic.email.notice.activated',
                'msgVars' => array(
                    '%name%' => $entity->getName(),
                    '%id%'   => $objectId
                )
            );

            $postActionVars['viewParameters']  = array(
                'objectAction' => 'view',
                'objectId'     => $objectId
            );
            $postActionVars['returnUrl']       = $this->generateUrl('mautic_page_action', $postActionVars['viewParameters']);
            $postActionVars['contentTemplate'] = 'MauticEmailBundle:Page:view';

        } //else don't do anything

        return $this->postActionRedirect(
            array_merge(
                $postActionVars,
                array(
                    'flashes' => $flashes
                )
            )
        );
    }

    /**
     * Manually sends emails
     *
     * @param $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function sendAction($objectId)
    {
        /** @var \Mautic\EmailBundle\Model\EmailModel $model */
        $model   = $this->factory->getModel('email');
        $entity  = $model->getEntity($objectId);
        $session = $this->factory->getSession();
        $page    = $session->get('mautic.email.page', 1);

        //set the return URL
        $returnUrl = $this->generateUrl('mautic_email_index', array('page' => $page));

        $postActionVars = array(
            'returnUrl'       => $returnUrl,
            'viewParameters'  => array('page' => $page),
            'contentTemplate' => 'MauticEmailBundle:Email:index',
            'passthroughVars' => array(
                'activeLink'    => 'mautic_email_index',
                'mauticContent' => 'email'
            )
        );

        //not found
        if ($entity === null) {
            return $this->postActionRedirect(
                array_merge(
                    $postActionVars,
                    array(
                        'flashes' => array(
                            array(
                                'type'    => 'error',
                                'msg'     => 'mautic.email.error.notfound',
                                'msgVars' => array('%id%' => $objectId)
                            )
                        )
                    )
                )
            );
        } elseif ($entity->getEmailType() == 'template' || !$this->factory->getSecurity()->hasEntityAccess('email:emails:viewown', 'email:emails:viewother', $entity->getCreatedBy())) {
            return $this->accessDenied();
        }

        //make sure email and category are published
        $category     = $entity->getCategory();
        $catPublished = (!empty($category)) ? $category->isPublished() : true;
        $published    = $entity->isPublished();

        if (!$catPublished || !$published) {
            return $this->postActionRedirect(
                array_merge(
                    $postActionVars,
                    array(
                        'flashes' => array(
                            array(
                                'type'    => 'error',
                                'msg'     => 'mautic.email.error.send',
                                'msgVars' => array('%name%' => $entity->getName())
                            )
                        )
                    )
                )
            );
        }

        $action   = $this->generateUrl('mautic_email_action', array('objectAction' => 'send', 'objectId' => $objectId));
        $pending  = $model->getPendingLeads($entity, null, true);
        $form     = $this->get('form.factory')->create('batch_send', array(), array('action' => $action));
        $complete = $this->request->request->get('complete', false);

        if ($this->request->getMethod() == 'POST' && ($complete || $this->isFormValid($form))) {
            if (!$complete) {
                $progress = array(0, (int) $pending);
                $session->set('mautic.email.send.progress', $progress);

                $stats = array('sent' => 0, 'failed' => 0, 'failedRecipients' => array());
                $session->set('mautic.email.send.stats', $stats);

                $status     = 'inprogress';
                $batchlimit = $form['batchlimit']->getData();

                $session->set('mautic.email.send.active', false);
            } else {
                $stats      = $session->get('mautic.email.send.stats');
                $progress   = $session->get('mautic.email.send.progress');
                $batchlimit = 100;
                $status     = (!empty($stats['failed'])) ? 'with_errors' : 'success';
            }

            $contentTemplate = 'MauticEmailBundle:Send:progress.html.php';
            $viewParameters  = array(
                'progress'   => $progress,
                'stats'      => $stats,
                'status'     => $status,
                'email'      => $entity,
                'batchlimit' => $batchlimit
            );

        } else {
            //process and send
            $contentTemplate = 'MauticEmailBundle:Send:form.html.php';
            $viewParameters  = array(
                'form'    => $form->createView(),
                'email'   => $entity,
                'pending' => $pending
            );
        }

        return $this->delegateView(
            array(
                'viewParameters'  => $viewParameters,
                'contentTemplate' => $contentTemplate,
                'passthroughVars' => array(
                    'mauticContent' => 'emailSend',
                    'route'         => $action
                )
            )
        );
    }


    /**
     * Send example email to current user
     *
     * @param $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function exampleAction($objectId)
    {
        /** @var \Mautic\EmailBundle\Model\EmailModel $model */
        $model  = $this->factory->getModel('email');
        $entity = $model->getEntity($objectId);

        //not found or not allowed
        if ($entity === null
            || (!$this->factory->getSecurity()->hasEntityAccess(
                'email:emails:viewown',
                'email:emails:viewother',
                $entity->getCreatedBy()
            ))
        ) {
            return $this->viewAction($objectId);
        }

        // Prepare a fake lead
        /** @var \Mautic\LeadBundle\Model\FieldModel $fieldModel */
        $fieldModel   = $this->factory->getModel('lead.field');
        $fields       = $fieldModel->getFieldList(false, false);
        array_walk($fields, function(&$field) {
            $field = "[$field]";
        });
        $fields['id'] = 0;

        // Send to current user
        $user  = $this->factory->getUser();
        $users = array(
            array(
                'id'        => $user->getId(),
                'firstname' => $user->getFirstName(),
                'lastname'  => $user->getLastName(),
                'email'     => $user->getEmail()
            )
        );

        // Send to current user
        $model->sendEmailToUser($entity, $users, $fields, array(), array(), false);

        $this->addFlash('mautic.email.notice.test_sent.success');

        return $this->viewAction($objectId);
    }

    /**
     * Deletes a group of entities
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function batchDeleteAction()
    {
        $page      = $this->factory->getSession()->get('mautic.email.page', 1);
        $returnUrl = $this->generateUrl('mautic_email_index', array('page' => $page));
        $flashes   = array();

        $postActionVars = array(
            'returnUrl'       => $returnUrl,
            'viewParameters'  => array('page' => $page),
            'contentTemplate' => 'MauticEmailBundle:Email:index',
            'passthroughVars' => array(
                'activeLink'    => '#mautic_email_index',
                'mauticContent' => 'email'
            )
        );

        if ($this->request->getMethod() == 'POST') {
            $model     = $this->factory->getModel('email');
            $ids       = json_decode($this->request->query->get('ids', '{}'));

            $deleteIds = array();

            // Loop over the IDs to perform access checks pre-delete
            foreach ($ids as $objectId) {
                $entity = $model->getEntity($objectId);

                if ($entity === null) {
                    $flashes[] = array(
                        'type'    => 'error',
                        'msg'     => 'mautic.email.error.notfound',
                        'msgVars' => array('%id%' => $objectId)
                    );
                } elseif (!$this->factory->getSecurity()->hasEntityAccess(
                    'email:emails:viewown',
                    'email:emails:viewother',
                    $entity->getCreatedBy()
                )
                ) {
                    $flashes[] = $this->accessDenied(true);
                } elseif ($model->isLocked($entity)) {
                    $flashes[] = $this->isLocked($postActionVars, $entity, 'email', true);
                } else {
                    $deleteIds[] = $objectId;
                }
            }

            // Delete everything we are able to
            if (!empty($deleteIds)) {
                $entities = $model->deleteEntities($deleteIds);

                $flashes[] = array(
                    'type'    => 'notice',
                    'msg'     => 'mautic.email.notice.batch_deleted',
                    'msgVars' => array(
                        '%count%' => count($entities)
                    )
                );
            }
        } //else don't do anything

        return $this->postActionRedirect(
            array_merge(
                $postActionVars,
                array(
                    'flashes' => $flashes
                )
            )
        );
    }

    /**
     * Preview email
     *
     * @param $objectId
     *
     * @deprecated since 1.1.3
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function previewAction($objectId)
    {
        return $this->redirect(
            $this->generateUrl('mautic_email_preview', array('objectId' => $objectId))
        );
    }

}
