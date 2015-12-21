<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\LeadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\ApiBundle\Serializer\Driver\ApiMetadataDriver;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\FormEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Mautic\CoreBundle\Entity\IpAddress;
use Mautic\EmailBundle\Entity\DoNotEmail;
use Mautic\UserBundle\Entity\User;

/**
 * Class Lead
 *
 * @package Mautic\LeadBundle\Entity
 */
class Lead extends FormEntity
{
    /**
     * Used to determine social identity
     *
     * @var array
     */
    private $availableSocialFields = array();

    /**
     * @var int
     */
    private $id;

    /**
     * @var \Mautic\UserBundle\Entity\User
     */
    private $owner;

    /**
     * @var int
     */
    private $points = 0;

    /**
     * @var ArrayCollection
     */
    private $pointsChangeLog;

    /**
     * @var ArrayCollection
     */
    private $doNotEmail;

    /**
     * @var ArrayCollection
     */
    private $ipAddresses;

    /**
     * @var \DateTime
     */
    private $lastActive;

    /**
     * @var array
     */
    private $internal = array();

    /**
     * @var array
     */
    private $socialCache = array();

    /**
     * Just a place to store updated field values so we don't have to loop through them again comparing
     *
     * @var array
     */
    private $updatedFields = array();

    /**
     * Used to populate trigger color
     *
     * @var string
     */
    private $color;

    /**
     * Sets if the IP was just created by LeadModel::getCurrentLead()
     *
     * @var bool
     */
    private $newlyCreated = false;

    /**
     * @var \DateTime
     */
    private $dateIdentified;

    /**
     * @var ArrayCollection
     */
    private $notes;

    /**
     * Used by Mautic to populate the fields pulled from the DB
     *
     * @var array
     */
    protected $fields = array();

    /**
     * @var string
     */
    private $preferredProfileImage;

    /**
     * Changed to true if the lead was anonymous before updating fields
     *
     * @var null
     */
    private $wasAnonymous = null;

    /**
     * @var bool
     */
    public $imported = false;

    /**
     * @var ArrayCollection
     */
    private $tags;

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata (ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('leads')
            ->setCustomRepositoryClass('Mautic\LeadBundle\Entity\LeadRepository')
            ->addLifecycleEvent('checkDateIdentified', 'preUpdate')
            ->addLifecycleEvent('checkDateIdentified', 'prePersist');

        $builder->createField('id', 'integer')
            ->isPrimaryKey()
            ->generatedValue()
            ->build();

        $builder->createManyToOne('owner', 'Mautic\UserBundle\Entity\User')
            ->addJoinColumn('owner_id', 'id', true, false, 'SET NULL')
            ->build();

        $builder->createField('points', 'integer')
            ->build();

        $builder->createOneToMany('pointsChangeLog', 'PointsChangeLog')
            ->orphanRemoval()
            ->setOrderBy(array('dateAdded' => 'DESC'))
            ->mappedBy('lead')
            ->cascadeAll()
            ->fetchExtraLazy()
            ->build();

        $builder->createOneToMany('doNotEmail', 'Mautic\EmailBundle\Entity\DoNotEmail')
            ->orphanRemoval()
            ->mappedBy('lead')
            ->cascadePersist()
            ->fetchExtraLazy()
            ->build();

        $builder->createManyToMany('ipAddresses', 'Mautic\CoreBundle\Entity\IpAddress')
            ->setJoinTable('lead_ips_xref')
            ->addInverseJoinColumn('ip_id', 'id', false)
            ->addJoinColumn('lead_id', 'id', false, false, 'CASCADE')
            ->setIndexBy('ipAddress')
            ->cascadeMerge()
            ->cascadePersist()
            ->cascadeDetach()
            ->build();

        $builder->createField('lastActive', 'datetime')
            ->columnName('last_active')
            ->nullable()
            ->build();

        $builder->createField('internal', 'array')
            ->nullable()
            ->build();

        $builder->createField('socialCache', 'array')
            ->columnName('social_cache')
            ->nullable()
            ->build();

        $builder->createField('dateIdentified', 'datetime')
            ->columnName('date_identified')
            ->nullable()
            ->build();

        $builder->createOneToMany('notes', 'LeadNote')
            ->orphanRemoval()
            ->setOrderBy(array('dateAdded' => 'DESC'))
            ->mappedBy('lead')
            ->fetchExtraLazy()
            ->build();

        $builder->createField('preferredProfileImage', 'string')
            ->columnName('preferred_profile_image')
            ->nullable()
            ->build();

        $builder->createManyToMany('tags', 'Mautic\LeadBundle\Entity\Tag')
            ->setJoinTable('lead_tags_xref')
            ->addInverseJoinColumn('tag_id', 'id', false)
            ->addJoinColumn('lead_id', 'id', false, false, 'CASCADE')
            ->setOrderBy(array('tag' => 'ASC'))
            ->setIndexBy('tag')
            ->fetchLazy()
            ->cascadeMerge()
            ->cascadePersist()
            ->cascadeDetach()
            ->build();
    }

    /**
     * Prepares the metadata for API usage
     *
     * @param $metadata
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata)
    {
        $metadata->setGroupPrefix('lead')
            ->setRoot('lead')
            ->addListProperties(
                array(
                    'id',
                    'points',
                    'color',
                    'fields',
                )
            )
            ->addProperties(
                array(
                    'lastActive',
                    'owner',
                    'ipAddresses',
                    'tags',
                    'dateIdentified',
                    'preferredProfileImage'
                )
            )
            ->build();
    }

    /**
     * @param string $prop
     * @param mixed  $val
     */
    protected function isChanged($prop, $val)
    {
        $getter  = "get".ucfirst($prop);
        $current = $this->$getter();
        if ($prop == 'owner') {
            if ($current && !$val) {
                $this->changes['owner'] = array($current->getName().' ('.$current->getId().')', $val);
            } elseif (!$current && $val) {
                $this->changes['owner'] = array($current, $val->getName().' ('.$val->getId().')');
            } elseif ($current && $val && $current->getId() != $val->getId()) {
                $this->changes['owner'] = array(
                    $current->getName().'('.$current->getId().')',
                    $val->getName().'('.$val->getId().')'
                );
            }
        } elseif ($prop == 'ipAddresses') {
            $this->changes['ipAddresses'] = array('', $val->getIpAddress());
        } elseif ($prop == 'tags') {
            if ($val instanceof Tag) {
                $this->changes['tags']['added'][] = $val->getTag();
            } else {
                $this->changes['tags']['removed'][] = $val;
            }
        } elseif ($this->$getter() != $val) {
            $this->changes[$prop] = array($this->$getter(), $val);
        }
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ipAddresses     = new ArrayCollection();
        $this->doNotEmail      = new ArrayCollection();
        $this->pointsChangeLog = new ArrayCollection();
        $this->tags            = new ArrayCollection();
    }

    /**
     * @return array
     */
    public function convertToArray ()
    {
        return get_object_vars($this);
    }

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return Lead
     */
    public function setId ($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId ()
    {
        return $this->id;
    }

    /**
     * Set owner
     *
     * @param User $owner
     *
     * @return Lead
     */
    public function setOwner(User $owner = null)
    {
        $this->isChanged('owner', $owner);
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return User
     */
    public function getOwner ()
    {
        return $this->owner;
    }

    /**
     * Add ipAddress
     *
     * @param IpAddress $ipAddress
     *
     * @return Lead
     */
    public function addIpAddress(IpAddress $ipAddress)
    {
        if (!$ipAddress->isTrackable()) {
            return $this;
        }

        $ip = $ipAddress->getIpAddress();
        if (!isset($this->ipAddresses[$ip])) {
            $this->isChanged('ipAddresses', $ipAddress);
            $this->ipAddresses[$ip] = $ipAddress;
        }

        return $this;
    }

    /**
     * Remove ipAddress
     *
     * @param IpAddress $ipAddress
     */
    public function removeIpAddress(IpAddress $ipAddress)
    {
        $this->ipAddresses->removeElement($ipAddress);
    }

    /**
     * Get ipAddresses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIpAddresses ()
    {
        return $this->ipAddresses;
    }

    /**
     * Get full name
     *
     * @param bool $lastFirst
     *
     * @return string
     */
    public function getName ($lastFirst = false)
    {
        if (isset($this->updatedFields['firstname'])) {
            $firstName = $this->updatedFields['firstname'];
        } else {
            $firstName = (isset($this->fields['core']['firstname']['value'])) ? $this->fields['core']['firstname']['value'] : '';
        }

        if (isset($this->updatedFields['lastname'])) {
            $lastName = $this->updatedFields['lastname'];
        } else {
            $lastName = (isset($this->fields['core']['lastname']['value'])) ? $this->fields['core']['lastname']['value'] : '';
        }

        $fullName  = "";
        if ($lastFirst && !empty($firstName) && !empty($lastName)) {
            $fullName = $lastName.", ".$firstName;
        } elseif (!empty($firstName) && !empty($lastName)) {
            $fullName = $firstName." ".$lastName;
        } elseif (!empty($firstName)) {
            $fullName = $firstName;
        } elseif (!empty($lastName)) {
            $fullName = $lastName;
        }

        return $fullName;
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany()
    {
        if (isset($this->updatedFields['company'])) {

            return $this->updatedFields['company'];
        }

        if (!empty($this->fields['core']['company']['value'])) {

            return $this->fields['core']['company']['value'];
        }

        return '';
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        if (isset($this->updatedFields['email'])) {

            return $this->updatedFields['email'];
        }

        if (!empty($this->fields['core']['email']['value'])) {

            return $this->fields['core']['email']['value'];
        }

        return '';
    }

    /**
     * Get lead field value
     *
     * @param      $field
     * @param null $group
     *
     * @return bool
     */
    public function getFieldValue($field, $group = null)
    {
        if (isset($this->updatedFields[$field])) {

            return $this->updatedFields[$field];
        }

        if (!empty($group) && isset($this->fields[$group][$field])) {

            return $this->fields[$group][$field]['value'];
        }

        foreach ($this->fields as $group => $groupFields) {
            foreach ($groupFields as $name => $details) {
                if ($name == $field) {

                    return $details['value'];
                }
            }
        }

        return false;
    }

    /**
     * Get the primary identifier for the lead
     *
     * @param bool $lastFirst
     *
     * @return string
     */
    public function getPrimaryIdentifier ($lastFirst = false)
    {
        if ($name = $this->getName($lastFirst)) {
            return $name;
        } elseif (!empty($this->fields['core']['company']['value'])) {
            return $this->fields['core']['company']['value'];
        } elseif (!empty($this->fields['core']['email']['value'])) {
            return $this->fields['core']['email']['value'];
        } elseif (count($ips = $this->getIpAddresses())) {
            return $ips->first()->getIpAddress();
        } elseif ($socialIdentity = $this->getFirstSocialIdentity()) {
            return $socialIdentity;
        } else {
            return 'mautic.lead.lead.anonymous';
        }
    }

    /**
     * Get the secondary identifier for the lead; mainly company
     *
     * @return string
     */
    public function getSecondaryIdentifier ()
    {
        if (!empty($this->fields['core']['company']['value'])) {
            return $this->fields['core']['company']['value'];
        }

        return '';
    }

    /**
     * Get the location for the lead
     *
     * @return string
     */
    public function getLocation ()
    {
        $location = '';

        if (!empty($this->fields['core']['city']['value'])) {
            $location .= $this->fields['core']['city']['value'].', ';
        }

        if (!empty($this->fields['core']['state']['value'])) {
            $location .= $this->fields['core']['state']['value'].', ';
        }

        if (!empty($this->fields['core']['country']['value'])) {
            $location .= $this->fields['core']['country']['value'].', ';
        }

        return rtrim($location, ', ');
    }

    /**
     * Adds/substracts from current points
     *
     * @param $points
     */
    public function addToPoints ($points)
    {
        $newPoints = $this->points + $points;
        $this->setPoints($newPoints);
    }

    /**
     * Set points
     *
     * @param integer $points
     *
     * @return Lead
     */
    public function setPoints ($points)
    {
        $this->isChanged('points', $points);
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer
     */
    public function getPoints ()
    {
        return $this->points;
    }

    /**
     * Creates a points change entry
     *
     * @param           $type
     * @param           $name
     * @param           $action
     * @param           $pointsDelta
     * @param IpAddress $ip
     */
    public function addPointsChangeLogEntry ($type, $name, $action, $pointsDelta, IpAddress $ip)
    {
        if ($pointsDelta <= 0) {
            // No need to record this

            return;
        }

        //create a new points change event
        $event = new PointsChangeLog();
        $event->setType($type);
        $event->setEventName($name);
        $event->setActionName($action);
        $event->setDateAdded(new \DateTime());
        $event->setDelta($pointsDelta);
        $event->setIpAddress($ip);
        $event->setLead($this);
        $this->addPointsChangeLog($event);
    }

    /**
     * Add pointsChangeLog
     *
     * @param PointsChangeLog $pointsChangeLog
     *
     * @return Lead
     */
    public function addPointsChangeLog(PointsChangeLog $pointsChangeLog)
    {
        $this->pointsChangeLog[] = $pointsChangeLog;

        return $this;
    }

    /**
     * Remove pointsChangeLog
     *
     * @param PointsChangeLog $pointsChangeLog
     */
    public function removePointsChangeLog(PointsChangeLog $pointsChangeLog)
    {
        $this->pointsChangeLog->removeElement($pointsChangeLog);
    }

    /**
     * Get pointsChangeLog
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPointsChangeLog ()
    {
        return $this->pointsChangeLog;
    }

    /**
     * @param DoNotEmail $doNotEmail
     *
     * @return $this
     */
    public function addDoNotEmailEntry(DoNotEmail $doNotEmail)
    {
        if ($doNotEmail->getBounced()) {
            $type = $doNotEmail->isManual() ? 'manual' : 'bounced';
        } elseif ($doNotEmail->getUnsubscribed()) {
            $type = 'unsubscribed';
        }

        $this->changes['dnc_status'] = array($type, $doNotEmail->getComments());

        $this->doNotEmail[] = $doNotEmail;

        return $this;
    }

    /**
     * @param DoNotEmail $doNotEmail
     */
    public function removeDoNotEmailEntry(DoNotEmail $doNotEmail)
    {
        if ($doNotEmail->getBounced()) {
            $type = $doNotEmail->isManual() ? 'manual' : 'bounced';
        } elseif ($doNotEmail->getUnsubscribed()) {
            $type = 'unsubscribed';
        }

        $this->changes['dnc_status'] = array('removed', $type);

        $this->doNotEmail->removeElement($doNotEmail);
    }

    /**
     * @return ArrayCollection
     */
    public function getDoNotEmail()
    {
        return $this->doNotEmail;
    }

    /**
     * Set internal storage
     *
     * @param $internal
     */
    public function setInternal ($internal)
    {
        $this->internal = $internal;
    }

    /**
     * Get internal storage
     *
     * @return mixed
     */
    public function getInternal ()
    {
        return $this->internal;
    }

    /**
     * Set social cache
     *
     * @param $cache
     */
    public function setSocialCache ($cache)
    {
        $this->socialCache = $cache;
    }

    /**
     * Get social cache
     *
     * @return mixed
     */
    public function getSocialCache ()
    {
        return $this->socialCache;
    }

    /**
     * @param $fields
     */
    public function setFields ($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @param bool $ungroup
     *
     * @return array
     */
    public function getFields ($ungroup = false)
    {
        if ($ungroup && isset($this->fields['core'])) {
            $return = array();
            foreach ($this->fields as $group => $fields) {
                $return += $fields;
            }

            return $return;
        }

        return $this->fields;
    }

    /**
     * Add an updated field to persist to the DB and to note changes
     *
     * @param        $alias
     * @param        $value
     * @param string $oldValue
     */
    public function addUpdatedField ($alias, $value, $oldValue = '')
    {
        if ($this->wasAnonymous == null) {
            $this->wasAnonymous = $this->isAnonymous();
        }
        $this->changes['fields'][$alias] = array($oldValue, $value);
        $this->updatedFields[$alias]     = $value;
    }

    /**
     * Get the array of updated fields
     *
     * @return array
     */
    public function getUpdatedFields ()
    {
        return $this->updatedFields;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return bool
     */
    public function isAnonymous()
    {
        if (
        $name = $this->getName()
            || !empty($this->updatedFields['firstname'])
            || !empty($this->updatedFields['lastname'])
            || !empty($this->updatedFields['company'])
            || !empty($this->updatedFields['email'])
            || !empty($this->fields['core']['company']['value'])
            || !empty($this->fields['core']['email']['value'])
            || $socialIdentity = $this->getFirstSocialIdentity()
        ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return bool
     */
    protected function getFirstSocialIdentity()
    {
        if (isset($this->fields['social'])) {
            foreach ($this->fields['social'] as $social) {
                if (!empty($social['value'])) {
                    return $social['value'];
                }
            }
        } elseif (!empty($this->updatedFields)) {
            foreach ($this->availableSocialFields as $social) {
                if (!empty($this->updatedFields[$social])) {
                    return $this->updatedFields[$social];
                }
            }
        }

        return false;
    }

    /**
     * @return boolean
     */
    public function isNewlyCreated()
    {
        return $this->newlyCreated;
    }

    /**
     * @param boolean $newlyCreated
     */
    public function setNewlyCreated($newlyCreated)
    {
        $this->newlyCreated = $newlyCreated;
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $source
     *
     * @return void
     */
    public function setPreferredProfileImage ($source)
    {
        $this->preferredProfileImage = $source;
    }

    /**
     * @return string
     */
    public function getPreferredProfileImage ()
    {
        return $this->preferredProfileImage;
    }

    /**
     * @return mixed
     */
    public function getDateIdentified()
    {
        return $this->dateIdentified;
    }

    /**
     * @param mixed $dateIdentified
     */
    public function setDateIdentified($dateIdentified)
    {
        $this->dateIdentified = $dateIdentified;
    }

    /**
     * Set date identified
     */
    public function checkDateIdentified ()
    {
        if ($this->dateIdentified == null && $this->wasAnonymous) {
            //check the changes to see if the user is now known
            if (!$this->isAnonymous()) {
                $this->dateIdentified            = new \DateTime();
                $this->changes['dateIdentified'] = array('', $this->dateIdentified);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getLastActive()
    {
        return $this->lastActive;
    }

    /**
     * @param mixed $lastActive
     */
    public function setLastActive($lastActive)
    {
        $this->changes['dateLastActive'] = array($this->lastActive, $lastActive);
        $this->lastActive                = $lastActive;
    }

    /**
     * @param array $availableSocialFields
     */
    public function setAvailableSocialFields(array $availableSocialFields)
    {
        $this->availableSocialFields = $availableSocialFields;
    }

    /**
     * Add tag
     *
     * @param Tag $tag
     *
     * @return Lead
     */
    public function addTag(Tag $tag)
    {
        $this->isChanged('tags', $tag);
        $this->tags[$tag->getTag()] = $tag;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param Tag $tag
     */
    public function removeTag(Tag $tag)
    {
        $this->isChanged('tags', $tag->getTag());
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return mixed
     */
    public function getTags ()
    {
        return $this->tags;
    }

    /**
     * Set tags
     *
     * @param $tags
     *
     * @return $this
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }
}
