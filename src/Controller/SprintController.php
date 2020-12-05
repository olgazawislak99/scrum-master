<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Sprint;
use App\Form\SprintType;
use Doctrine\ORM\Query\Expr\Math;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;


class SprintController extends AbstractController
{

    /**
     * @Route("/create-sprint", name="create-sprint")
     * @param Request $request
     * @param UserInterface $loggedUser
     */

    public function createGoal(Request $request, UserInterface $loggedUser)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $projects = $entityManager->getRepository(Project::class)->findAllUsersProjects($loggedUser);
        foreach ($projects as $project){
            if($project->getIsActual() === true){
                $actual = $project;
            }
        }
        $sprint = new Sprint();
        $form = $this->createForm(SprintType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $formData = $form->getData();
            $name = $formData['name'];
            $start = \DateTime::createFromFormat('d-m-Y', $formData['startDate']);
            $end = \DateTime::createFromFormat('d-m-Y', $formData['endDate']);
            $sprint->setName($name);
            $sprint->setStartDate($start);
            $sprint->setEndDate($end);
            $sprint->setProject($actual);
            $difference = date_diff($end, $start);

            if ($this->isValid($form, $difference)) {
                $sprint->setIsDone(0);
                $entityManager->persist($sprint);
                $entityManager->flush();
                return $this->redirectToRoute('sprint-management', ['sprintId' => $sprint->getId()]);
            } else {
                $this->addFlash(
                    'notice',
                    'Niepoprawne dane'
                );
            }
        }

        return $this->render('create-sprint.html.twig', [
            'projects' =>$projects,
            'message' => $message ?? null,
            'sprintForm' => $form->createView()
        ]);
    }

    private function isValid(FormInterface $form, \DateInterval $difference)
    {
        if ($form->isValid() && $this->checkSprintDuration($difference)) {
            return true;
        }
        return false;
    }

    private function checkSprintDuration(\DateInterval $difference)
    {
        if (!($difference->m > 0 || $difference->d > 6)) {
            return true;
        }
        return false;
    }

}