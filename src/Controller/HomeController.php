<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Goal;
use App\Repository\GoalRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class HomeController extends AbstractController
{

    /**
     * @var GoalRepository
     */
    private $goalRepository;

    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->goalRepository = $entityManager->getRepository(Goal::class);
        $this->projectRepository = $entityManager->getRepository(Project::class);
    }

    /**
     * @Route("/", name="home")
     * @param UserInterface $user
     * @return Response
     */

    public function home(UserInterface $user)
    {
        $projects = $this->projectRepository->findAllUsersProjects($user);
        foreach ($projects as $project){
            if($project->getIsActual() === true){
                $actual = $project;
            }
        }
        $goals = $this->goalRepository->findAllUsersGoals($user);

        return $this->render('home.html.twig', [
            'projects' => $projects,
            'goals' => $goals,
        ]);
    }

    /**
     * @Route("/rest/api/goal/{goalId}/", methods={"PUT"}, name="rest-api-goal-update")
     */
    public function updateGoal(int $goalId)
    {
        $request = Request::createFromGlobals();
        $response = new Response();
        $responseResult = [];
        $isDone = $request->get('isDone');
        $em = $this->getDoctrine()->getManager();
        $goal = $em->getRepository(Goal::class)->find($goalId);
        $goal->setIsDone($isDone);
        $responseResult['isDone'] = $isDone;
        $em->flush();
        ResponseHelper::defineSuccess($responseResult);

        $response->setContent(json_encode([
            $responseResult
        ]));
        return $response;
    }

    /**
     * @Route("/rest/api/subGoal/{subGoalId}/", methods={"PUT"}, name="rest-api-subGoal")
     */
    public function updateSubGoal(int $subGoalId)
    {
        $request = Request::createFromGlobals();
        $response = new Response();
        $responseResult = [];
        $isDone = $request->get('isDone');
        $em = $this->getDoctrine()->getManager();
        $subGoal = $em->getRepository(Goal::class)->find($subGoalId);
        if ($isDone === 'false') {
            $isDone = 0;
        }
        $subGoal->setIsDone($isDone);
        $responseResult['isDone'] = $isDone;
        $em->flush();
        ResponseHelper::defineSuccess($responseResult);

        $response->setContent(json_encode([
            $responseResult
        ]));
        return $response;
    }


    /**
     * @Route("/rest/api/project/{projectId}/", methods={"PUT"}, name="rest-api-project-update")
     * @param int $projectId
     * @param UserInterface $loggedUser
     */
    public function updateProject(int $projectId, UserInterface $loggedUser){
        $em = $this->getDoctrine()->getManager();
        $project = $this->projectRepository->find($projectId);
        $projects = $this->projectRepository->findAllUsersProjects($loggedUser);
        foreach ($projects as $p){
            if($p->getIsActual() === true){
                $p->setIsActual(false);
                $em->persist($p);
            }
        }
        $project->setIsActual(true);
        $em->persist($project);
        $em->flush();
        return new Response();
    }
}