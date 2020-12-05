<?php

namespace App\Controller;

use App\Entity\Goal;
use App\Entity\Project;
use App\Entity\User;
use App\Form\AddUserFormType;
use App\Repository\GoalRepository;
use App\Repository\UserRepository;
use App\Service\ResponseHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class GoalManagementController extends AbstractController
{
    /**
     * @var GoalRepository
     */
    private $goalRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->goalRepository = $entityManager->getRepository(Goal::class);
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    /**
     * @Route("/goal-management/{goalId}",  name="goal-management")
     * @param int $goalId
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */
    public function getGoal(int $goalId, Request $request, UserInterface $user)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Goal $goal */
        $goal = $em->getRepository(Goal::class)->find($goalId);
        $goalUsers = $this->userRepository->findAllGoalUsers($goal);
        $projects = $em->getRepository(Project::class)->findAllUsersProjects($user);
        $form = $this->createForm(AddUserFormType::class, $goal, ['users' => $goalUsers]);
        $form->handleRequest($request);
        $this->handleAddUserForm($form, $goal);
        $goalUsers = $this->userRepository->findAllGoalUsers($goal);
        $form = $this->createForm(AddUserFormType::class, $goal, ['users' => $goalUsers]);
        $desc = $goal->getGoalDesc();

        $response = $this->render('goal-management.twig', [
            'projects' => $projects,
            'goal' => $goal,
            'form' => $form->createView(),
            'goalUsers' => $goalUsers,
            'desc' => $desc
        ]);
        $response->headers->set("Cache-Control", 'no-store, no-cache, must-revalidate');

        return $response;
    }

    /**
     * @Route("/usersGoals/{userId}/{goalId}",  name="user-goal-delete")
     * @param $userId
     * @param $goalId
     * @return Response
     */
    public function deleteUserFormGoal(int $userId, int $goalId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($userId);
        $goal = $entityManager->getRepository(Goal::class)->find($goalId);
        /** @var Goal $goal */
        $goal->removeUser($user);
        if (count($goal->getUsers()) == 0) {
            $goal->setInBacklog(true);
        }
        $entityManager->persist($goal);
        $entityManager->flush();
        return $this->redirectToRoute('goal-management', ['goalId' => $goalId]);
    }

    /**
     * @Route("/rest/api/goalDesc/{goalId}/")
     */
    public function updateDesc(int $goalId)
    {
        $request = Request::createFromGlobals();
        $response = new Response();
        $responseResult = [];
        $entityManager = $this->getDoctrine()->getManager();
        $goal = $entityManager->getRepository(Goal::class)->find($goalId);
        $desc = $request->get('desc');
        $responseResult['desc'] = $desc;
        /** @var Goal $goal */
        $goal->setGoalDesc($desc);
        $entityManager->persist($goal);
        $entityManager->flush();
        ResponseHelper::defineSuccess($responseResult);
        $response->setContent(json_encode([
            $responseResult
        ]));

        return $response;
    }

    private function handleAddUserForm(FormInterface $form, Goal $goal){
        if ($form->isSubmitted() && $form->isValid()) {
            $users = $form->get('users')->getData();
            foreach ($users as $user) {
                $goal->addUser($user);
            }
            $goal->setInBacklog(false);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($goal);
            $entityManager->flush();
        }
    }

}