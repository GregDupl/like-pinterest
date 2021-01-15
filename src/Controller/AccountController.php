<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\UserFormType;
use App\Form\ChangePasswordFormType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="account")
     */
    public function show(): Response
    {
        return $this->render('account/show.html.twig');
    }

    /**
     * @Route("/account/edit", name="edit_account")
     */
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $em->flush();

            $this->addFlash('success', 'Profile updated successfully !');

            return $this->redirectToRoute('account');
         }

        return $this->render('account/edit.html.twig', [
          'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/change-password", name="account_change_password")
     */
    public function changePassword(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(ChangePasswordFormType::class, null,[
          'current_pass' => true
        ]);
        $user = $this->getUser();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $pass = $form['plainPassword']->getData();
          $encode_pass = $passwordEncoder->encodePassword($user, $pass);
          $user->setPassword($encode_pass);

          $em->flush();

          $this->addFlash('success', 'Password updated successfully !');

          return $this->redirectToRoute('account');
        }


        return $this->render('account/change_password.html.twig',[
          'form' => $form->createView()
        ]);
    }
}
