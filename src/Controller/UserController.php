<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\UserType;
use App\Form\ForgotPasswordType;
use App\Form\ResetPasswordType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use App\Event\Events;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\FormError;

class UserController extends Controller
{
    public function login(AuthenticationUtils $helper)
    {
        return $this->render('Security/login.html.twig', [
            'last_username' => $helper->getLastUsername(),
            'error' => $helper->getLastAuthenticationError(),
        ]);
    }

    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }

    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EventDispatcherInterface $eventDispatcher
    ) {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $user->setRoles(['ROLE_USER']);
            $user->setValidationToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $event = new GenericEvent($user);
            $eventDispatcher->dispatch(Events::USER_REGISTERED, $event);

            return $this->redirectToRoute('register_confirm');
        }

        return $this->render(
            'Registration/register.html.twig',
            ['form' => $form->createView()]
        );
    }

    public function confirmUser($token)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->findOneByValidationToken($token);

        if (is_null($user)) {
            throw new NotFoundHttpException('Token invalide');
        } else {
            $user->setValidationToken(null);
            $user->setIsActive(true);

            $em->persist($user);
            $em->flush();

            return $this->render(
                'Registration/confirm_user.html.twig',
                []
            );
        }
    }

    public function forgotPassword(Request $request, EventDispatcherInterface $eventDispatcher)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ForgotPasswordType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $username = $form->getData()['username'];
            $user = $em->getRepository(User::class)->findOneByUsername($username);

            if (is_null($user)) {
                $form->get('username')->addError(new FormError("Ce pseudo n'existe pas"));
            } else {
                $user->setResetToken(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));

                $em->persist($user);
                $em->flush();

                $event = new GenericEvent($user);
                $eventDispatcher->dispatch(Events::FORGOT_PASSWORD, $event);

                return $this->redirectToRoute('forgot_password_confirm');
            }
        }

        return $this->render(
            'Password/forgot_password.html.twig',
            ['form' => $form->createView()]
        );
    }

    public function resetPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, $token)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneByResetToken($token);
        $emailUser = $user->getEmail();

        if (is_null($user)) {
            throw new NotFoundHttpException('Token invalide');
        }

        $form = $this->createForm(ResetPasswordType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($emailUser != $form->getData()['email']) {
                $form->get('email')->addError(new FormError("Ce n'est pas la bonne adresse email"));
            } else {
                $password = $passwordEncoder->encodePassword($user, $form->getData()['password']);
                $user->setPassword($password);
                $user->setResetToken(null);

                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('reset_password_confirm');
            }
        }

        return $this->render(
            'Password/reset_password.html.twig',
            ['form' => $form->createView()]
        );
    }

    public function resetPasswordConfirm()
    {
        return $this->render('Password/reset_password_confirm.html.twig');
    }

    public function forgotPasswordConfirm()
    {
        return $this->render('Password/forgot_password_confirm.html.twig');
    }

    public function registerConfirm()
    {
        return $this->render('Registration/register_confirm.html.twig');
    }
}
