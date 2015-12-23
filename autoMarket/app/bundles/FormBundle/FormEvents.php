<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\FormBundle;

/**
 * Class FormEvents
 *
 * Events available for FormBundle
 */
final class FormEvents
{
    /**
     * The mautic.form_pre_save event is dispatched right before a form is persisted.
     *
     * The event listener receives a Mautic\FormBundle\Event\FormEvent instance.
     *
     * @var string
     */
    const FORM_PRE_SAVE = 'mautic.form_pre_save';

    /**
     * The mautic.form_post_save event is dispatched right after a form is persisted.
     *
     * The event listener receives a Mautic\FormBundle\Event\FormEvent instance.
     *
     * @var string
     */
    const FORM_POST_SAVE = 'mautic.form_post_save';

    /**
     * The mautic.form_pre_delete event is dispatched before a form is deleted.
     *
     * The event listener receives a Mautic\FormBundle\Event\FormEvent instance.
     *
     * @var string
     */
    const FORM_PRE_DELETE = 'mautic.form_pre_delete';

    /**
     * The mautic.form_post_delete event is dispatched after a form is deleted.
     *
     * The event listener receives a Mautic\FormBundle\Event\FormEvent instance.
     *
     * @var string
     */
    const FORM_POST_DELETE = 'mautic.form_post_delete';

    /**
     * The mautic.form_on_build event is dispatched before displaying the form builder form to allow adding of custom form
     * fields and submit actions
     *
     * The event listener receives a Mautic\FormBundle\Event\FormBuilderEvent instance.
     *
     * @var string
     */
    const FORM_ON_BUILD = 'mautic.form_on_build';

    /**
     * The mautic.form_on_submit event is dispatched when a new submission is fired
     *
     * The event listener receives a Mautic\FormBundle\Event\SubmissionEvent instance.
     *
     * @var string
     */
    const FORM_ON_SUBMIT = 'mautic.form_on_submit';
}
