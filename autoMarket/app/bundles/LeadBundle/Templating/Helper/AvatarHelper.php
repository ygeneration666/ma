<?php
/**
 * @package     Mautic
 * @copyright   2015 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\LeadBundle\Templating\Helper;

use Mautic\CoreBundle\Factory\MauticFactory;
use Mautic\LeadBundle\Entity\Lead;
use Symfony\Component\Templating\Helper\Helper;

class AvatarHelper extends Helper
{
    /**
     * @var MauticFactory
     */
    private $factory;

    /**
     * @param MauticFactory $factory
     */
    public function __construct (MauticFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param Lead $lead
     *
     * @return mixed
     */
    public function getAvatar(Lead $lead)
    {
        $preferred  = $lead->getPreferredProfileImage();
        $socialData = $lead->getSocialCache();
        $leadEmail  = $lead->getEmail();

        if ($preferred == 'custom' ) {
            if ($fmtime = filemtime($this->getAvatarPath(true) . '/avatar'.$lead->getId())) {
                // Append file modified time to ensure the latest is used by browser
                $img = $this->factory->getHelper('template.assets')->getUrl(
                    $this->getAvatarPath().'/avatar'.$lead->getId() . '?' . $fmtime,
                    null,
                    null,
                    false,
                    true
                );
            }
        } elseif (isset($socialData[$preferred]) && !empty($socialData[$preferred]['profile']['profileImage'])) {
            $img = $socialData[$preferred]['profile']['profileImage'];
        }

        if (empty($img)) {
            // Default to gravatar if others failed
            if (!empty($leadEmail)) {
                $img = $this->factory->getHelper('template.gravatar')->getImage($leadEmail);
            } else {
                $img = $this->getDefaultAvatar();
            }
        }

        return $img;
    }

    /**
     * Get avatar path
     *
     * @param $absolute
     *
     * @return string
     */
    public function getAvatarPath($absolute = false)
    {
        $imageDir = $this->factory->getSystemPath('images', $absolute);

        return $imageDir.'/lead_avatars';
    }

    /**
     * @param bool|false $absolute
     *
     * @return mixed
     */
    public function getDefaultAvatar($absolute = false)
    {
        return $this->factory->getHelper('template.assets')->getUrl(
            $this->factory->getSystemPath('assets').'/images/avatar.png',
            null,
            null,
            $absolute
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lead_avatar';
    }
}