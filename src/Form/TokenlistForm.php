<?php

namespace App\Form;

use App\Form\Type\TokenlistNetworkType;
use App\Form\Type\TokenlistReferType;
use App\Form\Type\TokenlistTagType;
use App\Form\Type\TokenlistTokenType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TokenlistForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('network', TokenlistNetworkType::class)
            ->add('refer', TokenlistReferType::class)
            ->add('tag', TokenlistTagType::class)
            ->add('token', TokenlistTokenType::class);
    }
}
