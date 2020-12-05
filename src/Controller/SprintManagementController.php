<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Sprint;
use App\Entity\Goal;
use App\Entity\User;
use App\Form\AddUserFormType;
use App\Repository\GoalRepository;
use App\Repository\UserRepository;
use App\Service\ResponseHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class SprintManagementController extends AbstractController
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
     * @Route("/sprint-management/{sprintId}", name="sprint-management")
     * @param int $sprintId
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */
    public function getSprint(int $sprintId, Request $request, UserInterface $user)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Sprint $sprint */
        $sprint = $em->getRepository(Sprint::class)->find($sprintId);
        $goals = $em->getRepository(Goal::class)->findBy(['sprint' => $sprint]);
        $projects = $em->getRepository(Project::class)->findAllUsersProjects($user);

        $response = $this->render('sprint-management.html.twig', [
            'projects' => $projects,
            'sprint' => $sprint,
            'goals' => $goals,
        ]);
        $response->headers->set("Cache-Control", 'no-cache, no-store, must-revalidate');
        return $response;
    }

    /**
     * @Route("/rest/api/goal/", methods={"POST"}, name="rest-api-goal-add")
     */
    public function addGoal()
    {
        dump('x');
        $request = Request::createFromGlobals();
        $response = new Response();
        $goal = new Goal;
        $entityManager = $this->getDoctrine()->getManager();
        $name = $request->get('name');
        $desc = $request->get('goalDesc');
        $sprintId = $request->get('sprintId');
        if ((!empty($name)) && $sprintId != null) {
            $goal->setName($name);
            $responseResult['name'] = $name;
            /** @var Sprint $sprint */
            $sprint = $entityManager->getRepository(Sprint::class)->find($sprintId);
            $goal->setSprint($sprint);
            $goal->setGoalDesc($desc);
            $entityManager->persist($goal);
            $goal->setIsDone(false);
            $goal->setInBacklog(true);
            $entityManager->flush();
            $responseResult['id'] = $goal->getId();
        }
        ResponseHelper::defineSuccess($responseResult);
        $response->setContent(json_encode([
            $responseResult
        ]));

        return $response;
    }


    /**
     * @Route("/rest/api/goal/{goalId}/", methods={"DELETE"}, name="rest-api-goal-delete")
     * @param $goalId
     * @return Response
     */
    public function deleteGoal($goalId)
    {
        $response = new Response();
        $entityManager = $this->getDoctrine()->getManager();
        $goal = $entityManager->getRepository(Goal::class)->find($goalId);
        $entityManager->remove($goal);
        $responseResult['deletedGoal'] = $goal;
        ResponseHelper::defineSuccess($responseResult);
        $entityManager->flush();
        $response->setContent(json_encode([
            $responseResult
        ]));
        return $response;
    }

}