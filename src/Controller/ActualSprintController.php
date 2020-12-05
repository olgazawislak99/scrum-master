<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Sprint;
use App\Entity\Goal;
use App\Repository\SprintRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ActualSprintController extends AbstractController
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
     * @Route("/actual-sprints/{week}", name="actual-sprint")
     * @param int $week
     * @param UserInterface $user
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function getActualSprint(int $week, UserInterface $user)
    {
        $lastWeek = $week - 1;
        $nextWeek = $week + 1;
        $firstDayOfWeek = date("Y-m-d", strtotime('monday this week +' . $week . 'week'));
        $lastDayOfWeek = date("Y-m-d", strtotime('monday next week +' . $week . 'week'));
        $em = $this->getDoctrine()->getManager();
        $projects = $em->getRepository(Project::class)->findAllUsersProjects($user);
        foreach ($projects as $project){
            if($project->getIsActual() === true){
                $actual = $project;
            }
        }
        $sprint = $this->sprintRepository->findActualWeekSprint($firstDayOfWeek, $lastDayOfWeek, $actual);
        $goals = $this->getDoctrine()->getRepository(Goal::class)->findAll();

        return $this->render('actual-sprints.html.twig', [
            'projects' => $projects,
            'sprint' => $sprint,
            'prev' => $lastWeek,
            'next' => $nextWeek,
            'goals' => $goals,
        ]);
    }
}