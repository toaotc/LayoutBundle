<?php

namespace Toa\Bundle\LayoutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Form\FormError;

/**
 * SecurityController
 *
 * @author Enrico Thies <enrico.thies@gmail.com>
 */
class SecurityController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/login/")
     * @Template()
     *
     * @return array
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();
        $translator = $this->container->get('translator');

        $form = $this->container->get('form.factory')
            ->createNamedBuilder(
                '',
                'form',
                array(
                    '_username' => null !== $session ? $session->get(SecurityContext::LAST_USERNAME) : null
                ),
                array(
                    'action' => $this->generateUrl('toa_layout_security_check'),
                    'method' => 'POST',
                    'intention' => 'authenticate',
                    'translation_domain' => 'toa_layout'
                )
            )
            ->add('_username', 'text')
            ->add('_password', 'password')
            ->add('_remember_me', 'checkbox', array('required' => false))
            ->add('login', 'submit')
            ->getForm();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = null;
        }

        if ($error) {
            $errorMessage = $translator->trans($error->getMessage(), array(), 'toa_layout');

            $form->addError(new FormError($errorMessage));
            $form->get('_username')->addError(new FormError(''));
            $form->get('_password')->addError(new FormError(''));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/login/check")
     */
    public function checkAction()
    {
    }

    /**
     * @Route("/logout")
     */
    public function logoutAction()
    {
    }
}
