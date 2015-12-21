<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 *
 */

namespace Mautic\ApiBundle\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use JMS\Serializer\SerializationContext;
use Mautic\ApiBundle\Serializer\Exclusion\PublishDetailsExclusionStrategy;
use Mautic\CoreBundle\Controller\MauticController;
use Mautic\CoreBundle\Factory\MauticFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class CommonApiController
 */
class CommonApiController extends FOSRestController implements MauticController
{

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var MauticFactory $factory
     */

    protected $factory;

    /**
     * @var \Mautic\CoreBundle\Security\Permissions\CorePermissions $security
     */
    protected $security;

    /**
     * Model object for processing the entity
     *
     * @var \Mautic\CoreBundle\Model\CommonModel
     */
    protected $model;

    /**
     * Key to return for a single entity
     *
     * @var string
     */
    protected $entityNameOne;

    /**
     * Key to return for entity lists
     *
     * @var string
     */
    protected $entityNameMulti;

    /**
     * Class for the entity
     *
     * @var string
     */
    protected $entityClass;

    /**
     * Permission base for the entity such as page:pages
     *
     * @var string
     */
    protected $permissionBase;

    /**
     * Used to set default filters for entity lists such as restricting to owning user
     *
     * @var array
     */
    protected $listFilters = array();

    /**
     * @var array
     */
    protected $serializerGroups = array();

    /**
     * Initialize some variables
     *
     * @param FilterControllerEvent $event
     *
     * @return void
     */
    public function initialize(FilterControllerEvent $event)
    {
        $this->security = $this->factory->getSecurity();
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param MauticFactory $factory
     */
    public function setFactory(MauticFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Obtains a list of entities as defined by the API URL
     *
     * @return Response
     */
    public function getEntitiesAction()
    {
        $repo       = $this->model->getRepository();
        $tableAlias = $repo->getTableAlias();

        $publishedOnly = $this->request->get('published', 0);
        if ($publishedOnly) {
            $this->listFilters[] = array(
                'column' => $tableAlias.'.isPublished',
                'expr'   => 'eq',
                'value'  => true
            );
        }

        $args    = array(
            'start'          => $this->request->query->get('start', 0),
            'limit'          => $this->request->query->get('limit', $this->factory->getParameter('default_pagelimit')),
            'filter'         => array(
                'string' => $this->request->query->get('search', ''),
                'force'  => $this->listFilters
            ),
            'orderBy'        => $this->request->query->get('orderBy', ''),
            'orderByDir'     => $this->request->query->get('orderByDir', 'ASC'),
            'withTotalCount' => true //for repositories that break free of Paginator
        );
        $results = $this->model->getEntities($args);

        list($entities, $totalCount) = $this->prepareEntitiesForView($results);

        $view = $this->view(
            array(
                'total'                => $totalCount,
                $this->entityNameMulti => $entities
            ),
            Codes::HTTP_OK
        );
        $this->setSerializationContext($view);

        return $this->handleView($view);
    }

    /**
     * Obtains a specific entity as defined by the API URL
     *
     * @param int $id Entity ID
     *
     * @return Response
     */
    public function getEntityAction($id)
    {
        $entity = $this->model->getEntity($id);
        if (!$entity instanceof $this->entityClass) {
            return $this->notFound();
        }

        if (!$this->checkEntityAccess($entity, 'view')) {
            return $this->accessDenied();
        }

        $this->preSerializeEntity($entity);
        $view = $this->view(array($this->entityNameOne => $entity), Codes::HTTP_OK);
        $this->setSerializationContext($view);

        return $this->handleView($view);
    }

    /**
     * Creates a new entity
     *
     * @return Response
     */
    public function newEntityAction()
    {
        $entity = $this->model->getEntity();

        if (!$this->checkEntityAccess($entity, 'create')) {
            return $this->accessDenied();
        }

        $parameters = $this->request->request->all();

        return $this->processForm($entity, $parameters, 'POST');
    }


    /**
     * Edits an existing entity or creates one on PUT if it doesn't exist
     *
     * @param int $id Entity ID
     *
     * @return Response
     */
    public function editEntityAction($id)
    {
        $entity     = $this->model->getEntity($id);
        $parameters = $this->request->request->all();
        $method     = $this->request->getMethod();

        if ($entity === null) {
            if ($method === 'PATCH') {
                //PATCH requires that an entity exists
                return $this->notFound();
            }

            //PUT can create a new entity if it doesn't exist
            $entity = $this->model->getEntity();
            if (!$this->checkEntityAccess($entity, 'create')) {
                return $this->accessDenied();
            }
        }

        if (!$this->checkEntityAccess($entity, 'edit')) {
            return $this->accessDenied();
        }

        return $this->processForm($entity, $parameters, $method);
    }

    /**
     * Deletes an entity
     *
     * @param int $id Entity ID
     *
     * @return Response
     */
    public function deleteEntityAction($id)
    {
        $entity = $this->model->getEntity($id);
        if (null !== $entity) {
            if (!$this->checkEntityAccess($entity, 'delete')) {
                return $this->accessDenied();
            }

            $this->model->deleteEntity($entity);

            $this->preSerializeEntity($entity);
            $view = $this->view(array($this->entityNameOne => $entity), Codes::HTTP_OK);
            $this->setSerializationContext($view);

            return $this->handleView($view);
        }

        return $this->notFound();
    }

    /**
     * Processes API Form
     *
     * @param        $entity
     * @param null   $parameters
     * @param string $method
     *
     * @return Response
     */
    protected function processForm($entity, $parameters = null, $method = 'PUT')
    {
        if ($parameters === null) {
            //get from request
            $parameters = $this->request->request->all();
        }

        //unset the ID in the parameters if set as this will cause the form to fail
        if (isset($parameters['id'])) {
            unset($parameters['id']);
        }

        //is an entity being updated or created?
        if ($entity->getId()) {
            $statusCode = Codes::HTTP_OK;
            $action     = 'edit';
        } else {
            $statusCode = Codes::HTTP_CREATED;
            $action     = 'new';
        }

        $form         = $this->createEntityForm($entity);
        $submitParams = $this->prepareParametersForBinding($parameters, $entity, $action);
        $form->submit($submitParams, 'PATCH' !== $method);

        if ($form->isValid()) {
            $this->preSaveEntity($entity, $form, $parameters, $action);
            $this->model->saveEntity($entity);
            $headers = array();
            //return the newly created entities location if applicable
            if (Codes::HTTP_CREATED === $statusCode) {
                $headers['Location'] = $this->generateUrl(
                    'mautic_api_get'.$this->entityNameOne,
                    array('id' => $entity->getId()),
                    true
                );
            }
            $this->preSerializeEntity($entity, $action);

            $view = $this->view(array($this->entityNameOne => $entity), $statusCode, $headers);
            $this->setSerializationContext($view);
        } else {
            $view = $this->view($form, Codes::HTTP_BAD_REQUEST);
        }

        return $this->handleView($view);
    }

    /**
     * Checks if user has permission to access retrieved entity
     *
     * @param mixed  $entity
     * @param string $action view|create|edit|publish|delete
     *
     * @return bool
     */
    protected function checkEntityAccess($entity, $action = 'view')
    {
        if ($action != 'create') {
            $ownPerm   = "{$this->permissionBase}:{$action}own";
            $otherPerm = "{$this->permissionBase}:{$action}other";

            return $this->security->hasEntityAccess($ownPerm, $otherPerm, $entity->getCreatedBy());
        }

        return $this->security->isGranted("{$this->permissionBase}:create");
    }

    /**
     * Creates the form instance
     *
     * @param $entity
     *
     * @return mixed
     */
    protected function createEntityForm($entity)
    {
        return $this->model->createForm($entity, $this->get('form.factory'));
    }

    /**
     * Set serialization groups and exclusion strategies
     *
     * @param \FOS\RestBundle\View\View $view
     *
     * @return void
     */
    protected function setSerializationContext(&$view)
    {
        $context = SerializationContext::create();
        if (!empty($this->serializerGroups)) {
            $context->setGroups($this->serializerGroups);
        }

        //Only include FormEntity properties for the top level entity and not the associated entities
        $context->addExclusionStrategy(
            new PublishDetailsExclusionStrategy()
        );

        //include null values
        $context->setSerializeNull(true);

        $view->setSerializationContext($context);
    }

    /**
     * Convert posted parameters into what the form needs in order to successfully bind
     *
     * @param $parameters
     * @param $entity
     * @param $action
     *
     * @return mixed
     */
    protected function prepareParametersForBinding($parameters, $entity, $action)
    {
        return $parameters;
    }

    /**
     * Give the controller an opportunity to process the entity before persisting
     *
     * @param $entity
     * @param $form
     * @param $parameters
     * @param $action
     *
     * @return mixed
     */
    protected function preSaveEntity(&$entity, $form, $parameters, $action = 'edit')
    {
    }

    /**
     * Gives child controllers opportunity to analyze and do whatever to an entity before going through serializer
     *
     * @param        $entity
     * @param string $action
     *
     * @return mixed
     */
    protected function preSerializeEntity(&$entity, $action = 'view')
    {
    }

    /**
     * Gives child controllers opportunity to analyze and do whatever to an entity before populating the form
     *
     * @param        $entity
     * @param        $parameters
     * @param string $action
     *
     * @return mixed
     */
    protected function prePopulateForm(&$entity, $parameters, $action = 'edit')
    {
    }

    /**
     * Returns a 403 Access Denied
     *
     * @param string $msg
     *
     * @return Response
     */
    protected function accessDenied($msg = 'mautic.core.error.accessdenied')
    {
        $view = $this->view(
            array(
                'error' => array(
                    'code'    => Codes::HTTP_FORBIDDEN,
                    'message' => $this->get('translator')->trans($msg, array(), 'flashes')
                )
            ),
            Codes::HTTP_FORBIDDEN
        );

        return $this->handleView($view);
    }

    /**
     * Returns a 404 Not Found
     *
     * @param string $msg
     *
     * @return Response
     */
    protected function notFound($msg = 'mautic.core.error.notfound')
    {
        $view = $this->view(
            array(
                'error' => array(
                    'code'    => Codes::HTTP_NOT_FOUND,
                    'message' => $this->get('translator')->trans($msg, array(), 'flashes')
                )
            ),
            Codes::HTTP_NOT_FOUND
        );

        return $this->handleView($view);
    }

    /**
     * Returns a 400 Bad Request
     *
     * @param string $msg
     *
     * @return Response
     */
    protected function badRequest($msg = 'mautic.core.error.badrequest')
    {
        $view = $this->view(
            array(
                'error' => array(
                    'code'    => Codes::HTTP_BAD_REQUEST,
                    'message' => $this->get('translator')->trans($msg, array(), 'flashes')
                )
            ),
            Codes::HTTP_BAD_REQUEST
        );

        return $this->handleView($view);
    }

    /**
     * {@inheritdoc}
     *
     * @param null  $data
     * @param null  $statusCode
     * @param array $headers
     */
    protected function view($data = null, $statusCode = null, array $headers = array())
    {

        if ($data instanceof Paginator) {
            // Get iterator out of Paginator class so that the entities are properly serialized by the serializer
            $data = $data->getIterator()->getArrayCopy();
        }

        return parent::view($data, $statusCode, $headers);
    }

    /**
     * Prepares entities returned from repository getEntities()
     *
     * @param $results
     *
     * @return array($entities, $totalCount)
     */
    protected function prepareEntitiesForView($results)
    {

        if ($results instanceof Paginator) {
            $totalCount = count($results);
        } elseif (isset($results['count'])) {
            $totalCount = $results['count'];
            $results    = $results['results'];
        } else {
            $totalCount = count($results);
        }

        //we have to convert them from paginated proxy functions to entities in order for them to be
        //returned by the serializer/rest bundle
        $entities = array();
        foreach ($results as $r) {
            if (is_array($r) && isset($r[0])) {
                //entity has some extra something something tacked onto the entities
                if (is_object($r[0])) {
                    foreach ($r as $k => $v) {
                        if ($k === 0) {
                            continue;
                        }

                        $r[0]->$k = $v;
                    }
                    $this->preSerializeEntity($r[0]);
                    $entities[] = $r[0];
                } elseif (is_array($r[0])) {
                    foreach ($r[0] as $k => $v) {
                        $r[$k] = $v;
                    }
                    unset($r[0]);
                    $this->preSerializeEntity($r);
                    $entities[] = $r;
                }
            } else {
                $this->preSerializeEntity($r);
                $entities[] = $r;
            }
        }

        return array($entities, $totalCount);
    }
}