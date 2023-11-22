<?php

namespace App\Form;

use App\Entity\Transaction;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class TransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class
            ])
            ->add('value', ChoiceType::class, [
                'choices' => [
                    "+100" => 100,
                    "+75" => 75,
                    "+50" => 50,
                    "+30" => 30,
                    "+20" => 20,
                    "+10" => 10,
                    "+5" => 5,
                    "-5" => -5,
                    "-10" => -10,
                    "-20" => -20,
                    "-30" => -30,
                    "-50" => -50,
                    "-75" => -75,
                    "-100" => -100,
                ]

            ])

            ->add('description')
            ->add('media', DropzoneType::class, [
                'mapped' => false

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
