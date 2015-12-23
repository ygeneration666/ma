<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\LeadBundle\Form\Validator\Constraints;

use Mautic\CoreBundle\Factory\MauticFactory;
use Mautic\LeadBundle\Entity\LeadList;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class LeadListAccessValidator extends ConstraintValidator
{

    /**
     * @var MauticFactory
     */
    private $factory;

    public function __construct(MauticFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $listModel = $this->factory->getModel('lead.list');
        $lists     = $listModel->getUserLists();

        if (!count($value)) {
            $this->context->addViolation(
                $constraint->message,
                array('%string%' => '')
            );
        }

        foreach ($value as $l) {
            if (!isset($lists[$l->getId()])) {
                $this->context->addViolation(
                    $constraint->message,
                    array('%string%' => $l->getName())
                );
                break;
            }
        }
    }
}