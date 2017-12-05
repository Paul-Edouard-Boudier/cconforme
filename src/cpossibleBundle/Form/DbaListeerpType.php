<?php

namespace cpossibleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DbaListeerpType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('listeerpTypedossier')->add('listeerpIdAdap')->add('listeerpDemandeur')->add('listeErpNomErp')->add('listeerpNature')->add('listeerpCategorie')->add('listeerpType')->add('listeerpDateDeclaration')->add('listeerpDateValidAdap')->add('listeerpDelaiAdap')->add('listeerpIdIgn')->add('listeerpSiret')->add('listeerpNumeroVoie')->add('listeerpNumeroComplement')->add('listeerpComplementVoie')->add('listeerpNomVoie')->add('listeerpAliasNomVoie')->add('listeerpLieuDit')->add('listeerpCodePostal')->add('listeerpCodeInsee')->add('listeerpNomCommune')->add('listeerpDepartement')->add('listeerpStatut');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'cpossibleBundle\Entity\DbaListeerp'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cpossiblebundle_dbalisteerp';
    }


}
