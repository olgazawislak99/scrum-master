<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\String\u;

class AddUserFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $users = $options['users'];
        $users = (!empty($users)) ? $users : 0;
        $builder
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
                'multiple' => true,
                'label' => false,
                'expanded' =>true,
                'query_builder' => function (EntityRepository $er) use ($users) {
                     return $er->createQueryBuilder('u')
                        ->where('u.id NOT IN (:usersId)')
                        ->setParameter('usersId', $users)
                        ->orderBy("lower(substring(u.name,locate(' ', u.name) ))");
            }
            ])
            ->setMethod('PUT');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('users');
        $resolver->setAllowedTypes('users', array(User::class, 'int', 'array'));
    }
}