<?php


namespace App\Service;

use App\Entity\Goal;
use App\Repository\SprintRepository;
use Doctrine\ORM\EntityManagerInterface;

class GoalManager
{
    private  $em;
    private  $sprintRepository;

    public function __construct(EntityManagerInterface $em, SprintRepository $sprintRepository)
    {
        $this->em = $em;
        $this->sprintRepository = $sprintRepository;
    }

    public function deleteSprintFromGoal(){
        $start = date("Y-m-d", strtotime('monday this week'));
        $end = date("Y-m-d", strtotime('sunday this week'));
        $sprint = $this->sprintRepository->findActualWeekSprint($start, $end);
        $goals = $this->em->getRepository(Goal::class)->findBy(['sprint' => $sprint, 'inBacklog' => true]);
        if(!empty($sprint) && !empty($goals)){
            foreach ($goals as $goal){
                /** @var Goal $goal */
                $goal->setSprint(null);
                $this->em->persist($goal);

            }
            $this->em->flush();
            return true;
        }

        return false;
    }
}
