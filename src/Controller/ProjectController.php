<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Form\ProjectFormType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;


class ProjectController extends AbstractController
{
    /**
     * @Route("/add-project", name="add-project")
     * @param Request $request
     * @param UserInterface $loggedUser
     * @return Response
     */

    public function addProject(Request $request, UserInterface $loggedUser)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $project = new Project();
        $projects =  $entityManager->getRepository(Project::class)->findAllUsersProjects($loggedUser);
        $form = $this->createForm(ProjectFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            try{
                foreach ($projects as $p){
                    $p->setIsActual(false);
                    $entityManager->persist($p);
                    $entityManager->flush();
                }
                $formData = $form->getData();
                $name = $formData['name'];
                $usersEmails = $formData['users'];
                $users = $this->getUsers($usersEmails);
                $project->setName($name);
                $project->setOwnerUser($this->getUser());
                $project->setIsActual(true);
                $entityManager->persist($project);
                $entityManager->flush();
                $projectFromDb = $entityManager->getRepository(Project::class)->findOneBy(['name' => $name]);
                foreach ($users as $user) {
                    $projectFromDb->addUser($user);
                }
                $projectFromDb->addUser($loggedUser);
                $entityManager->flush();

                return $this->redirectToRoute('home');
            }catch (Exception $e){
                $this->addFlash('notice', $e->getMessage());
            }
        }

        return $this->render('add-project.html.twig', [
            'projects' => $projects,
            'message' => $message ?? null,
            'projectForm' => $form->createView(),]);
    }

    private function getUsers($usersEmails)
    {
        $em = $this->getDoctrine()->getManager();
        $users = [];
        $explodedUsers = explode(" ", $usersEmails);
        foreach ($explodedUsers as $userEmail) {
            /** @var User $goal */
            $user = $em->getRepository(User::class)->findOneBy(['email' => $userEmail]);
            if (empty($user)) {
                throw new Exception('There\'s no user with email: ' .$userEmail);
            }else{
                $users[] =$user;
            }
        }
        return $users;
    }
}