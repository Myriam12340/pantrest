<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CommentsRepository;
use App\Repository\ContactRepository;
use App\Repository\PinRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(UserRepository $userRepository ,ContactRepository $contactRepository ,CommentsRepository $commentsRepository ,PinRepository $pinRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'users' => $userRepository->findAll(),'notification' => $contactRepository->findAll(),'Comments'=>$commentsRepository->findAll(),'pin'=>$pinRepository->findAll(),
        ]);
    }

    /**
     * @Route("/users", name="app_users", methods={"GET"})
     */
    public function users(UserRepository $userRepository ,PinRepository  $pinRepository ): Response
    {

        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }


}
