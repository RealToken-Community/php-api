<?php

namespace App\Form\Type;

use App\Entity\TokenlistNetwork;
use App\Entity\TokenlistTag;
use App\Entity\TokenlistToken;
use App\Form\TokenlistForm;
use App\Repository\TokenlistTagRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TokenlistTokenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address')
            ->add('name')
            ->add('symbol')
            ->add('decimals')
//            ->add('tags', TokenlistTagType::class)
//            ->add('chain', TokenlistNetworkType::class)
            ->add('chain', TokenlistNetworkType::class, [
//                'class' => TokenlistNetwork::class,
//                'choice_label' => 'name',
                'data_class' => null,
                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
            ->add(
                'category', TokenlistTagType::class, array(
                'class' => 'TokenlistNetwork::class',
                'query_builder' => function (TokenlistTagRepository $tt) {
                    return $tt->createQueryBuilder('tt');
                })
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TokenlistToken::class,
        ]);
    }
}
