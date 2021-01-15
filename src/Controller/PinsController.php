<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(PinRepository $pinRepo): Response
    {
        #$pinRepo = $this->getDoctrine()->getRepository(Pin::class);
        $pins = $pinRepo->findBy([], ['createdAt'=> 'DESC']);
        
        return $this->render('pins/index.html.twig',[
          'pins' => $pins,
        ]);
    }

    /**
     * @Route("/pins/{id<[0-9]+>}", name="pins_show")
     */
    public function show(PinRepository $pinRepo, $id): Response
    {
      $pin = $pinRepo->find($id);

      return $this->render('pins/pin.html.twig',[
        'pin' => $pin,
      ]);
    }

    /**
     * @Route("/pins/create", name="pins_create")
     */
    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepo): Response
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

      $pin = new Pin;

      $form = $this->createForm(PinType::class, $pin);

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {

          $pin = $form->getData();

          $pin->setUser($this->getUser());

          $em->persist($pin);
          $em->flush();

          $this->addFlash('success', 'Pin successfully created !');

          return $this->redirectToRoute('home');
       }

      else {
        return $this->render("pins/create.html.twig", [
          'form' => $form->createView()
        ]);
      }
    }

    /**
     * @Route("/pins/edit/{id<[0-9]+>}", name="pins_edit")
     */
    public function edit(Request $request, $id, PinRepository $pinRepo, EntityManagerInterface $em): Response
    {
      $pin = $pinRepo->find($id);

      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

      if ($this->getUser() != $pin->getUser() ) {
        return $this->redirectToRoute('home');
      }

      $form = $this->createForm(PinType::class, $pin, [
          'method' => 'PUT'
      ]);

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $pin = $form->getData();

          $em->flush();

          $this->addFlash('success', 'Pin successfully updated !');

          return $this->redirectToRoute('home');
      }

      return $this->render('pins/edit.html.twig', [
          'pin' => $pin,
          'form' => $form->createView()
      ]);
    }


    /**
     * @Route("/pins/delete/{id<[0-9]+>}", name="pins_delete", methods={"DELETE"})
     */
    public function delete($id, Request $request, PinRepository $pinRepo, EntityManagerInterface $em): Response
    {

      if ($this->getUser() != $pin->getUser() ) {
        return $this->redirectToRoute('home');
      }

      $pin = $pinRepo->find($id);

      if ($this->isCsrfTokenValid('pin_deletion_'. $pin->getId(), $request->request->get('_token'))) {
        $em->remove($pin);
        $em->flush();

        $this->addFlash('info', 'Pin successfully deleted!');
      }

      return $this->redirectToRoute('home');

    }
}
