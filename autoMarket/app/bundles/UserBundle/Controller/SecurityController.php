<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\UserBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Exception as Exception;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class DefaultController
 */
class SecurityController extends CommonController
{

    /**
     * {@inheritdoc}
     */
    public function initialize (FilterControllerEvent $event)
    {
        $securityContext = $this->get('security.context');

        //redirect user if they are already authenticated
        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY') ||
            $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')
        ) {

            $redirectUrl = $this->generateUrl('mautic_dashboard_index');
            $event->setController(function () use ($redirectUrl) {
                return new RedirectResponse($redirectUrl);
            });
        }
    }

    /**
     * Generates login form and processes login
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function loginAction ()
    {
        // A way to keep the upgrade from failing if the session is lost after
        // the cache is cleared by upgrade.php
        if ($this->request->cookies->has('mautic_update')) {
            $step = $this->request->cookies->get('mautic_update');
            if ($step == 'clearCache') {
                // Run migrations
                $this->request->query->set('finalize', 1);
                return $this->forward('MauticCoreBundle:Ajax:updateDatabaseMigration',
                    array(
                        'request'  => $this->request
                    )
                );
            } elseif ($step == 'schemaMigration') {
                // Done so finalize
                return $this->forward('MauticCoreBundle:Ajax:updateFinalization',
                    array(
                        'request'  => $this->request
                    )
                );
            }

            /** @var \Mautic\CoreBundle\Helper\CookieHelper $cookieHelper */
            $cookieHelper = $this->factory->getHelper('cookie');
            $cookieHelper->deleteCookie('mautic_update');
        }

        $session = $this->request->getSession();

        // get the login error if there is one
        if ($this->request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $this->request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        if (!empty($error)) {
            if (($error instanceof Exception\BadCredentialsException)) {
                $msg = 'mautic.user.auth.error.invalidlogin';
            } elseif ($error instanceof Exception\DisabledException) {
                $msg = 'mautic.user.auth.error.disabledaccount';
            } else {
                $msg = $error->getMessage();
            }

            $this->addFlash($msg, array(), 'error', null, false);
        }
        $this->request->query->set('tmpl', 'login');

        return $this->delegateView(array(
            'viewParameters'  => array('last_username' => $session->get(SecurityContext::LAST_USERNAME)),
            'contentTemplate' => 'MauticUserBundle:Security:login.html.php',
            'passthroughVars' => array(
                'route'          => $this->generateUrl('login'),
                'mauticContent'  => 'user',
                'sessionExpired' => true
            )
        ));
    }
}
