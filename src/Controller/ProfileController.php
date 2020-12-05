<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Sprint;
use App\Entity\Goal;
use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\UploadPhotoFormType;
use App\Repository\GoalRepository;
use App\Repository\SprintRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileController extends AbstractController
{
    /**
     * @var GoalRepository
     */
    private $goalRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->goalRepository = $entityManager->getRepository(Goal::class);
    }

    /**
     * @Route("/profile", name="profile")
     * @param Request $request
     * @param SluggerInterface $slugger
     * @return Response
     */

    public function profile(Request $request, SluggerInterface $slugger)
    {
        $doneGoals = 0;
        $undoneGoals = 0;
        $userGoals = $this->goalRepository->findAllUsersGoals($this->getUser());
        foreach ($userGoals as $userGoal) {
            if ($userGoal->getIsDone() == 1) {
                $doneGoals += 1;
            } else $undoneGoals += 1;
        }

        $entityManager = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(UploadPhotoFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();
                $photo->move(
                    $this->getParameter('photos'),
                    $newFilename
                );
                $user->setPhotoFilename($newFilename);

                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirect($this->generateUrl('app_home_home'));
            }
        }

        $projects = $entityManager->getRepository(Project::class)->findAllUsersProjects($this->getUser());
        return $this->render('profile.html.twig', [
            'projects' => $projects,
            'doneGoals' => $doneGoals,
            'undoneGoals' => $undoneGoals,
            'error' => $error ?? false,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/rest/api/user/{userId}/password/", methods={"PUT"}, name="rest-api-password")
     * @param $userId
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function updatePassword(int $userId, UserPasswordEncoderInterface $passwordEncoder)
    {
        $request = Request::createFromGlobals();
        $response = new Response();
        $responseResult = [];
        $oldPwd = $request->get('oldPwd');
        $newPwd = $request->get('newPwd');
        $confirmPwd = $request->get('confirmPwd');
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $em->getRepository(User::class)->find($userId);
        if ($passwordEncoder->isPasswordValid($user, $oldPwd) && $newPwd == $confirmPwd && $newPwd != $oldPwd) {
            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $newPwd
            ));
            $em->flush();

            $responseResult['success'] = true;
        } else {
            $responseResult['success'] = false;
        }

        $response->setContent(json_encode([
            $responseResult
        ]));

        return $response;
    }
}