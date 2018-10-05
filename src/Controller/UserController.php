<?php

namespace App\Controller;

use App\Form\Type\UserRegisterForm;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Exception\UserAlreadyRegisteredException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/")
 */
class UserController extends Controller
{
    /**
     * @Route("/register", name="user-register")
     *
     * @Template(":User:register.html.twig")
     *
     * @param Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Exception
     */
    public function registerAction(Request $request)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(UserRegisterForm::class);
        if ($form->isSubmitted() && $request->isMethod($request::METHOD_POST)) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                try {
                    $this->getUserService()->register($form->getData());

                    return $this->redirectToRoute('user-register-success');
                } catch (UserAlreadyRegisteredException $e) {
                    $form->get('email')->addError(new FormError('User already register!'));
                } catch (\Exception $e) {
                    $message = 'Unfortunately registration failed. We have already received an issue notification and will try to fix it as soon as possible.';
                    $this->addFlash('error', $message);
                }
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/login", name="user-login")
     *
     * @Template(":User:login.html.twig")
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function loginAction()
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('homepage');
        }
        $authenticationUtils = $this->get('security.authentication_utils');
        $err = $authenticationUtils->getLastAuthenticationError();

        return [
            'error' => $err,
            'last_username' => $authenticationUtils->getLastUsername(),
        ];
    }

    /**
     * @Route("/register/success", name="user-register-success")
     *
     * @Template(":User:registerSuccess.html.twig")
     *
     * @return array|RedirectResponse
     */
    public function registerSuccessAction()
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('homepage');
        }
        $authenticationUtils = $this->get('security.authentication_utils');
        $err = $authenticationUtils->getLastAuthenticationError();

        return [
            'error' => $err,
            'last_username' => $authenticationUtils->getLastUsername(),
        ];
    }

    /**
     * @Route("/login-check", name="user-login-check")
     *
     * @return RedirectResponse
     */
    public function loginCheckAction()
    {
        return $this->redirectToRoute('user-login');
    }

    /**
     * @Route("/logout", name="frontend-user-logout")
     *
     * @return RedirectResponse
     */
    public function logoutAction()
    {
        return $this->redirectToRoute('user-login');
    }

    /**
     * @return UserService|object
     */
    protected function getUserService()
    {
        return $this->container->get('user.service');
    }
}
