<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CoreBundle\Event;

use Mautic\CoreBundle\Helper\BuilderTokenHelper;
use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class BuilderEvent
 *
 * @package Mautic\PageBundle\Event
 */
class BuilderEvent extends Event
{
    protected $tokens = array();
    protected $visualTokens = array();
    protected $tokenSections = array();
    protected $abTestWinnerCriteria = array();
    protected $translator;
    protected $entity = null;
    protected $requested;
    protected $tokenFilter;
    protected $tokenFilterText;
    protected $tokenFilterTarget;

    public function __construct($translator, $entity = null, $requested = 'all', $tokenFilter = '')
    {
        $this->translator        = $translator;
        $this->entity            = $entity;
        $this->requested         = $requested;
        $this->tokenFilterTarget = (strpos($tokenFilter, '{@') === 0) ? 'label' : 'token';
        $this->tokenFilterText   = str_replace(array('{@', '{', '}'), '', $tokenFilter);
        $this->tokenFilter       = ($this->tokenFilterTarget == 'label') ? $this->tokenFilterText : str_replace('{@', '{', $tokenFilter);
    }

    /**
     * @param $key
     * @param $header
     * @param $content
     * @param $priority
     */
    public function addTokenSection($key, $header, $content, $priority = 0)
    {
        if (array_key_exists($key, $this->tokenSections)) {
            throw new InvalidArgumentException("The key, '$key' is already used by another subscriber. Please use a different key.");
        }

        if (!empty($content)) {
            $header                    = $this->translator->trans($header);
            $this->tokenSections[$key] = array(
                'header'   => $header,
                'content'  => $content,
                'priority' => $priority
            );
        }
    }

    /**
     * Get tokenSections
     *
     * @return array
     */
    public function getTokenSections()
    {
        $sort = array();
        foreach ($this->tokenSections as $k => $v) {
            $sort['priority'][$k] = $v['priority'];
            $sort['header'][$k]   = $v['header'];
        }

        array_multisort($sort['priority'], SORT_DESC, $sort['header'], SORT_ASC, $this->tokenSections);

        return $this->tokenSections;
    }


    /**
     * Get list of AB Test winner criteria
     *
     * @return array
     */
    public function getAbTestWinnerCriteria()
    {
        uasort(
            $this->abTestWinnerCriteria,
            function ($a, $b) {
                return strnatcasecmp(
                    $a['group'],
                    $b['group']
                );
            }
        );
        $array = array('criteria' => $this->abTestWinnerCriteria);

        $choices = array();
        foreach ($this->abTestWinnerCriteria as $k => $c) {
            $choices[$c['group']][$k] = $c['label'];
        }
        $array['choices'] = $choices;

        return $array;
    }

    /**
     * Adds an A/B test winner criteria option
     *
     * @param string $key      - a unique identifier; it is recommended that it be namespaced i.e. lead.points
     * @param array  $criteria - can contain the following keys:
     *                         'group'           => (required) translation string to group criteria by in the dropdown select list
     *                         'label'           => (required) what to display in the list
     *                         'formType'        => (optional) name of the form type SERVICE for the criteria
     *                         'formTypeOptions' => (optional) array of options to pass to the formType service
     *                         'callback'        => (required) callback function that will be passed the parent page or email for winner determination
     *                         The callback function can receive the following arguments by name (via ReflectionMethod::invokeArgs())
     *                         array $properties - values saved from the formType as defined here; keyed by page or email id in the case of
     *                         multiple variants
     *                         Mautic\CoreBundle\Factory\MauticFactory $factory
     *                         Mautic\PageBundle\Entity\Page $page | Mautic\EmailBundle\Entity\Email $email (depending on the context)
     *                         Mautic\PageBundle\Entity\Page|Mautic\EmailBundle\Entity\Email $parent
     *                         Doctrine\Common\Collections\ArrayCollection $children
     */
    public function addAbTestWinnerCriteria($key, array $criteria)
    {
        if (array_key_exists($key, $this->abTestWinnerCriteria)) {
            throw new InvalidArgumentException("The key, '$key' is already used by another criteria. Please use a different key.");
        }

        //check for required keys and that given functions are callable
        $this->verifyCriteria(
            array('group', 'label', 'callback'),
            array('callback'),
            $criteria
        );

        //translate the group
        $criteria['group']                = $this->translator->trans($criteria['group']);
        $this->abTestWinnerCriteria[$key] = $criteria;
    }

    /**
     * @param array $keys
     * @param array $methods
     * @param array $criteria
     */
    private function verifyCriteria(array $keys, array $methods, array $criteria)
    {
        foreach ($keys as $k) {
            if (!array_key_exists($k, $criteria)) {
                throw new InvalidArgumentException("The key, '$k' is missing.");
            }
        }

        foreach ($methods as $m) {
            if (isset($criteria[$m]) && !is_callable($criteria[$m], true)) {
                throw new InvalidArgumentException(
                    $criteria[$m].' is not callable.  Please ensure that it exists and that it is a fully qualified namespace.'
                );
            }
        }
    }

    /**
     * @param array $tokens
     * @param bool  $allowVisualPlaceholder
     * @param bool  $convertToLinks
     */
    public function addTokens(array $tokens, $allowVisualPlaceholder = false, $convertToLinks = false)
    {
        if ($convertToLinks) {
            array_walk($tokens, function(&$val, $key) {
                $val = 'a:' . $val;
            });
        }

        $this->tokens = array_merge($this->tokens, $tokens);

        if ($allowVisualPlaceholder) {
            $this->visualTokens = array_merge($this->visualTokens, array_keys($tokens));
        }
    }

    /**
     * @param      $key
     * @param      $value
     * @param bool $allowVisualPlaceholder
     */
    public function addToken($key, $value, $allowVisualPlaceholder = false)
    {
        $this->tokens[$key] = $value;

        if ($allowVisualPlaceholder) {
            $this->visualTokens[] = $key;
        }
    }

    /**
     * Get token array
     *
     * @return array
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * @return array
     */
    public function getVisualTokens()
    {
        return $this->visualTokens;
    }

    /**
     * Check if tokens have been requested.
     *
     * @param null $tokenKeys Pass in string or array of tokens to filter against if filterType == token
     *
     * @return bool
     */
    public function tokensRequested($tokenKeys = null)
    {
        if ($requested = $this->getRequested('tokens')) {

            if (!empty($this->tokenFilter) && $this->tokenFilterTarget == 'token') {
                if (!is_array($tokenKeys)) {
                    $tokenKeys = array($tokenKeys);
                }

                $found = false;
                foreach ($tokenKeys as $token) {
                    if (stripos($token, $this->tokenFilter) === 0) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $requested = false;
                }
            }
        }

        return $requested;
    }

    /**
     * Get text of the search filter
     *
     * @return array
     */
    public function getTokenFilter()
    {
        return array(
            'target' => $this->tokenFilterTarget,
            'filter' => $this->tokenFilterText
        );
    }

    /**
     * Simple token filtering
     *
     * @param array $tokens array('token' => 'label')
     *
     * @return array
     */
    public function filterTokens($tokens)
    {
        $filter = $this->tokenFilter;

        if (empty($filter)) {
            return $tokens;
        }

        if ($this->tokenFilterTarget == 'label') {
            // Do a search against the label
            $tokens = array_filter(
                $tokens,
                function ($v) use ($filter) {
                    return (stripos($v, $filter) === 0);
                }
            );
        } else {
            // Do a search against the token
            $found = array_filter(
                array_keys($tokens),
                function ($k) use ($filter) {
                    return (stripos($k, $filter) === 0);
                }
            );

            $tokens = array_intersect_key($tokens, array_flip($found));
        }

        return $tokens;
    }

    /**
     * Add tokens from a BuilderTokenHelper
     *
     * @param BuilderTokenHelper $tokenHelper
     * @param                    $tokens
     * @param string             $labelColumn
     * @param string             $valueColumn
     * @param bool               $allowVisualPlaceholder If set to true, the description will be displayed in the editor instead of the raw token
     * @param bool               $convertToLinks         If true, the tokens will be converted to links
     *
     */
    public function addTokensFromHelper(
        BuilderTokenHelper $tokenHelper,
        $tokens,
        $labelColumn = 'name',
        $valueColumn = 'id',
        $allowVisualPlaceholder = false,
        $convertToLinks = false
    ) {
    	$tokens = $this->getTokensFromHelper($tokenHelper, $tokens, $labelColumn, $valueColumn);
		if ( $tokens == null ) {
			$tokens = array();
		}
		
        $this->addTokens(
        	$tokens,
            $allowVisualPlaceholder,
            $convertToLinks
        );
    }

    /**
     * Get tokens from a BuilderTokenHelper
     *
     * @param BuilderTokenHelper $tokenHelper
     * @param                    $tokens
     * @param                    $labelColumn
     * @param                    $valueColumn
     *
     * @return array|void
     */
    public function getTokensFromHelper(BuilderTokenHelper $tokenHelper, $tokens, $labelColumn = 'name', $valueColumn = 'id')
    {
        return $tokenHelper->getTokens(
            $tokens,
            ($this->tokenFilterTarget == 'label' ? $this->tokenFilterText : ''),
            $labelColumn,
            $valueColumn
        );
    }

    /**
     * Check if token sections have been requested
     *
     * @return bool
     */
    public function tokenSectionsRequested()
    {
        return $this->getRequested('tokenSections');
    }

    /**
     * Check if AB Test Winner Criteria has been requested
     *
     * @return bool
     */
    public function abTestWinnerCriteriaRequested()
    {
        return $this->getRequested('abTestWinnerCriteria');
    }

    /**
     * @param $type
     *
     * @return bool
     */
    protected function getRequested($type)
    {
        if (is_array($this->requested)) {
            return in_array($type, $this->requested);
        }

        return ($this->requested == $type || $this->requested == 'all');
    }
}