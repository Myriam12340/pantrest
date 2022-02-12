<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\CommentsRepository;
use App\Repository\PinRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use SebastianBergmann\CodeCoverage\Report\Text;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Constraints\Length;

class PinController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     *
     */
    public function index1(PinRepository $pinRepository): Response
    {
        $pins = $pinRepository->findBy(['audience'=>'Public'], ['createdAt' => 'DESC']);

        return $this->render('pin/home.html.twig', [
            'pins'=>$pins,

        ]);
    }

    /**
     * @Route("/mypins", name="app_mypin")
     *
     */
    public function mypin(PinRepository $pinRepository): Response
    {
        $pins = $pinRepository->findBy(['user'=>$this->getUser()], ['createdAt' => 'DESC']);

        return $this->render('pin/my_pins.html.twig', [
            'pins'=>$pins,

        ]);
    }



    /**
     * @Route("/pin/{id<[0-9]+>}", name="app_pin_show", methods="GET")
     *
     *
     */
    public function show(Pin $pin): Response
    {

        return $this->render('pin/show.html.twig', compact('pin'));
    }

    /**
     * @Route("/pin/create", name="app_pin_create",methods={"GET", "POST"})
     *@Security("is_granted('ROLE_USER') and user.isVerified()", statusCode=404, message="you're not allowed , please check your account mail")
     */
    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepo):Response{

            $pin = new Pin;

            $form = $this->createForm(PinType::class, $pin);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $pin->setUser($this->getUser());
                $em->persist($pin);
                $em->flush();

                $this->addFlash('success', 'Pin successfully created!');

                return $this->redirectToRoute('app_home');
            }

            return $this->render('pin/create.html.twig', [
                'form' => $form->createView()
            ]);
        }

    /**
     * @Route("/pin/{id<[0-9]+>}/edit", name="app_pin_edit", methods={"GET", "POST"})
     *@Security("is_granted('ROLE_USER') and pin.getUser()==user", statusCode=404, message="you're not allowed")
     */
    public function edit(Pin $pin , Request $request , EntityManagerInterface $em):Response
    {

        $form = $this->createForm(PinType::class , $pin);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $em->flush();
            $this->addFlash('success','pin modifier avec success');

            return $this->redirectToRoute('app_home');
        }
            return $this->render('pin/edit.html.twig', [
                'pin'=>$pin,
                'form' => $form->createView()
            ]);
        }
    /**
     * @Route("/pin/commentaire", name="app_pin_comm", methods={"GET", "POST"})
     *
     */
    public function new(ManagerRegistry $doctrine,Request $request, EntityManagerInterface $entityManager, UserRepository $userRepo , PinRepository $pinRepository): Response
    {

        $com = $request->request->get('com');


        $id = $request->request->get('id');
        $commentaire = new Comments();
        $commentaire->setContenu($com);
        $p = $doctrine->getRepository(Pin::class)->find($id);

        $commentaire->setUser($this->getUser());
        $commentaire->setPin($p);
        $entityManager->persist($commentaire);
        $entityManager->flush();

        return $this->redirectToRoute('app_pin_listecom', array('id' =>$id ));



    }


    /**
     * @Route("/pin/{id<[0-9]+>}/listcom", name="app_pin_listecom", methods={"GET", "POST"})
     *
     */
    public function listcom(ManagerRegistry $doctrine,Pin $pin , Request $request , EntityManagerInterface $entityManager , CommentsRepository $commentsRepository):Response
    {  $com = $request->request->get('com');
        $coms = $commentsRepository->findBy(['pin'=>$pin], ['createdAt' => 'DESC']);



        return $this->render('pin/listecom.html.twig', [
            'coms'=>$coms,
            'pin'=>$pin,

        ]);
    }


    /**
     * @Route("/pin/rechercher", name="app_pin_rechercher" ,methods={"GET","POST"})
     *
     */
    public function recherche(ManagerRegistry $doctrine,PinRepository $pinRepository  , Request $request  , EntityManagerInterface $em):Response
    {
        $re = $request->request->get('re');
        $ca = $request->request->get('ca');
        if ($re == "" && $ca != "") {
            $pin = $pinRepository->findBy(array('categorie' => $ca ,'audience'=>'Public'),['createdAt' => 'DESC']);


            return $this->render('pin/recherche.html.twig', [

                'pins' => $pin,

            ]);

        } else if ($re != "" && $ca == "") {
            /*test sur description et title*/
            $pin = $doctrine->getRepository(Pin::class)->findAllLIKEDESCRIPTION($re);
            return $this->render('pin/recherche.html.twig', [

                'pins' => $pin,

            ]);

        }
        else if ($re != "" && $ca != "") {
            /*test sur description et title et categorie*/
                        $pin = $doctrine->getRepository(Pin::class)->findAllby2($re,$ca);



            return $this->render('pin/recherche.html.twig', [

                'pins' => $pin,

            ]);

        }






        $pin = $pinRepository->findBy(array('audience'=>'Public'),['createdAt' => 'DESC']);


        return $this->render('pin/recherche.html.twig', [

            'pins' => $pin,

        ]);
    }
    /**
     * @Route("/{id}/delete", name="pin_delete", methods={"POST"})
     *
     */
    public function delete(Request $request, Pin $pin, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pin->getId(), $request->request->get('_token'))) {
        $entityManager->remove($pin);
        $entityManager->flush();
            $this->addFlash('info', 'Pin successfully deleted');
    }


        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }

}
