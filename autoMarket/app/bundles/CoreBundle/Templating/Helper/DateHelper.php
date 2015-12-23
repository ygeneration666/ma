<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CoreBundle\Templating\Helper;

use Mautic\CoreBundle\Factory\MauticFactory;
use Symfony\Component\Templating\Helper\Helper;

class DateHelper extends Helper
{
    /**
     * @var array
     */
    protected $formats;

    /**
     * @var \Mautic\CoreBundle\Helper\DateTimeHelper
     */
    protected $helper;

    /**
     * @var
     */
    protected $translator;

    /**
     * @param MauticFactory $factory
     */
    public function __construct(MauticFactory $factory)
    {
        $this->formats = array(
            'datetime' => $factory->getParameter('date_format_full'),
            'short'    => $factory->getParameter('date_format_short'),
            'date'     => $factory->getParameter('date_format_dateonly'),
            'time'     => $factory->getParameter('date_format_timeonly'),
        );

        $this->helper  = $factory->getDate();

        $this->translator = $factory->getTranslator();
    }

    /**
     * @param string           $type
     * @param \DateTime|string $datetime
     * @param string           $timezone
     * @param string           $fromFormat
     *
     * @return string
     */
    protected function format($type, $datetime, $timezone, $fromFormat)
    {
        if (empty($datetime)) {
            return '';
        } else {
            $this->helper->setDateTime($datetime, $fromFormat, $timezone);

            return $this->helper->toLocalString(
                $this->formats[$type]
            );
        }
    }

    /**
     * Returns full date. eg. October 8, 2014 21:19
     *
     * @param \DateTime|string $datetime
     * @param string           $timezone
     * @param string           $fromFormat
     *
     * @return string
     */
    public function toFull($datetime, $timezone = 'local', $fromFormat = 'Y-m-d H:i:s')
    {
        return $this->format('datetime', $datetime, $timezone, $fromFormat);
    }

    /**
     * Returns date and time concat eg 2014-08-02 5:00am
     *
     * @param \DateTime|string $datetime
     * @param string           $timezone
     * @param string           $fromFormat
     *
     * @return string
     */
    public function toFullConcat($datetime, $timezone = 'local', $fromFormat = 'Y-m-d H:i:s')
    {
        $this->helper->setDateTime($datetime, $fromFormat, $timezone);
        return $this->helper->toLocalString(
            $this->formats['date'] . ' ' . $this->formats['time']
        );
    }

    /**
     * Returns short date format eg Sun, Oct 8
     *
     * @param \DateTime|string $datetime
     * @param string           $timezone
     * @param string           $fromFormat
     *
     * @return string
     */
    public function toShort($datetime, $timezone = 'local', $fromFormat = 'Y-m-d H:i:s')
    {
        return $this->format('short', $datetime, $timezone, $fromFormat);
    }

    /**
     * Returns date only e.g. 2014-08-09
     *
     * @param \DateTime|string $datetime
     * @param string           $timezone
     * @param string           $fromFormat
     *
     * @return string
     */
    public function toDate($datetime, $timezone = 'local', $fromFormat = 'Y-m-d H:i:s')
    {
        return $this->format('date', $datetime, $timezone, $fromFormat);
    }

    /**
     * Returns time only e.g. 21:19
     *
     * @param \DateTime|string $datetime
     * @param string           $timezone
     * @param string           $fromFormat
     *
     * @return string
     */
    public function toTime($datetime, $timezone = 'local', $fromFormat = 'Y-m-d H:i:s')
    {
        return $this->format('time', $datetime, $timezone, $fromFormat);
    }

    /**
     * Returns date/time like Today, 10:00 AM
     *
     * @param        $datetime
     * @param string $timezone
     * @param string $fromFormat
     */
    public function toText($datetime, $timezone = 'local', $fromFormat = 'Y-m-d H:i:s')
    {
        if (empty($datetime)) {
            return '';
        }

        $this->helper->setDateTime($datetime, $fromFormat, $timezone);

        $textDate = $this->helper->getTextDate();
        $dt = $this->helper->getLocalDateTime();

        if ($textDate) {
            return $this->translator->trans('mautic.core.date.' . $textDate, array('%time%' => $dt->format("g:i a")));
        } else {
            $interval = $this->helper->getDiff('now', null, true);

            return $this->translator->trans('mautic.core.date.ago', array('%days%' => $interval->days));
        }
    }

    /**
     * Format DateInterval into humanly readable format.
     * Example: 55 minutes 49 seconds.
     * It doesn't return zero values like 0 years.
     *
     * @param   DateInterval    $range
     * @param   string          $format
     *
     * @return  string          $formatedRange
     */
    public function formatRange($range, $format = null)
    {
        if ($range instanceof \DateInterval) {

            $formated = array();
            $timeUnits = array('y' => 'year', 'm' => 'month', 'd' => 'day', 'h' => 'hour', 'i' => 'minute', 's' => 'second');

            foreach ($timeUnits as $key => $unit) {
                if ($range->{$key}) {
                    $formated[] = $this->translator->transChoice(
                        'mautic.core.date.' . $unit,
                        $range->{$key},
                        array('%count%' => $range->{$key}));
                }
            }

            return implode(' ', $formated);
        }

        return '';
    }

    /**
     * @return string
     */
    public function getFullFormat()
    {
        return $this->formats['datetime'];
    }

    /**
     * @return string
     */
    public function getDateFormat()
    {
        return $this->formats['date'];
    }

    /**
     * @return string
     */
    public function getTimeFormat()
    {
        return $this->formats['time'];
    }

    /**
     * @return string
     */
    public function getShortFormat()
    {
        return $this->formats['short'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'date';
    }
}
