<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\ApiBundle\Entity\oAuth1;

use Bazinga\OAuthServerBundle\Model\ConsumerInterface;
use Bazinga\OAuthServerBundle\Model\RequestTokenInterface;
use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class RequestToken
 *
 * @package Mautic\ApiBundle\Entity\oAuth1
 */
class RequestToken implements RequestTokenInterface
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var Consumer
     */
    protected $consumer;

    /**
     * @var \Mautic\UserBundle\Entity\User
     */
    protected $user;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var integer
     */
    protected $expiresAt;

    /**
     * @var string
     */
    protected $verifier;

    /**
     * @param ORM\ClassMetadata $metadata
     */
    public static function loadMetadata (ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('oauth1_request_tokens')
            ->addIndex(array('token'), 'oauth1_request_token_search');

        $builder->createField('id', 'integer')
            ->isPrimaryKey()
            ->generatedValue()
            ->build();

        $builder->createManyToOne('consumer', 'Consumer')
            ->addJoinColumn('consumer_id', 'id', false, false, 'CASCADE')
            ->build();

        $builder->createManyToOne('user', 'Mautic\UserBundle\Entity\User')
            ->addJoinColumn('user_id', 'id', true, false, 'CASCADE')
            ->build();

        $builder->addField('token', 'string');

        $builder->addField('secret', 'string');

        $builder->createField('expiresAt', 'bigint')
            ->columnName('expires_at')
            ->build();

        $builder->addField('verifier', 'string');
    }

    /**
     * {@inheritDoc}
     */
    public function getId ()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function getToken ()
    {
        return $this->token;
    }

    /**
     * {@inheritDoc}
     */
    public function setToken ($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getSecret ()
    {
        return $this->secret;
    }

    /**
     * {@inheritDoc}
     */
    public function setSecret ($secret)
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getExpiresAt ()
    {
        return $this->expiresAt;
    }

    /**
     * {@inheritDoc}
     */
    public function setExpiresAt ($expiresAt)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getExpiresIn ()
    {
        if ($this->expiresAt) {
            return $this->expiresAt - time();
        }

        return PHP_INT_MAX;
    }

    /**
     * {@inheritDoc}
     */
    public function hasExpired ()
    {
        if ($this->expiresAt) {
            return time() > $this->expiresAt;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getUser ()
    {
        return $this->user;
    }

    /**
     * {@inheritDoc}
     */
    public function setUser (UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setConsumer (ConsumerInterface $consumer)
    {
        $this->consumer = $consumer;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getConsumer ()
    {
        return $this->consumer;
    }

    /**
     * {@inheritDoc}
     */
    public function getVerifier ()
    {
        return $this->verifier;
    }

    /**
     * {@inheritDoc}
     */
    public function setVerifier ($verifier)
    {
        $this->verifier = $verifier;
    }
}
