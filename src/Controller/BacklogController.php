<?php

namespace App\Controller;


use App\Entity\Project;
use App\Entity\Sprint;
use App\Repository\SprintRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Goal;
use Symfony\Component\Security\Core\User\UserInterface;


class BacklogController extends AbstractController
{
    /**
     * @var SprintRepository
     */
    private $sprintRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->sprintRepository = $entityManager->getRepository(Sprint::class);
    }

    /**
     * @Route("/backlog", name="backlog")
     * @param Request $request
     * @return Response
     */
    public function getGoals(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $projects = $em->getRepository(Project::class)->findAllUsersProjects($this->getUser());
        foreach ($projects as $project){
            if($project->getIsActual() === true){
                $actual = $project;
            }
        }
        $start = date("Y-m-d", strtotime('monday next week'));
        $end = date("Y-m-d", strtotime('sunday next week'));
        /** @var Sprint $sprint */
        $sprint = $this->sprintRepository->findActualWeekSprint($start, $end, $actual);
        $goalId = $request->get('goalId');
        if ($goalId != null && $sprint != null) {
            /** @var Goal $goal */
            $goal = $em->getRepository(Goal::class)->findOneBy(['id' => $goalId]);
            $goal->setSprint($sprint);

            $goal->setInBacklog(false);
            $em->persist($goal);
            $em->flush();
        }
        $backlogGoals = $em->getRepository(Goal::class)->findBy(['inBacklog' => true]);

        return $this->render('backlog.html.twig', [
            'projects' => $projects,
            'backlogGoals' => $backlogGoals,
        ]);
    }
}