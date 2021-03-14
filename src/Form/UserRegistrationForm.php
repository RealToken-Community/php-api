<?php

namespace App\Form;

use App\Form\Type\ApplicationType;
use App\Form\Type\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserRegistrationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', UserType::class)
            ->add('application', ApplicationType::class);
    }
}