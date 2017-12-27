<?php

namespace cpossibleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Choice;

class DbaListeerpType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('listeerpTypedossier', ChoiceType::class, array('choices' => array('adap_s' => 'adap_s', 'at_adap' => 'at_adap', 'attestation' => 'attestation', 'adap' => 'adap')))->add('listeerpIdAdap')->add('listeerpDemandeur')->add('listeErpNomErp')->add('listeerpNature', ChoiceType::class, array('choices' => array('erp' => 'erp', 'iop' => 'iop')))->add('listeerpCategorie', ChoiceType::class, array('choices' => array('1ère catégorie au dessus de 1500 personnes' => '1', '2ème catégorie de 700 à 1500 personnes' => '2', '3ème catégorie de 301 à 700 personnes' => '3', '4ème catégorie <= 300 personnes - sauf 5ème catégorie' => '4', '5ème catégorie < seuil dépendant de l\'établissement' => '5')))->add('listeerpType', ChoiceType::class, array('choices' => array('J' => "J", 'L' => "L", 'M' => "M", 'N' => "N", 'O' => "O", 'P' => "P", 'R' => "R", 'S' => "S", 'T' => "T", 'U' => "U", 'V' => "V", 'W' => "W", 'X' => "X", 'Y' => "Y", 'PA' => "PA", 'CTS' => "CTS", 'SG' => "SG", 'PS' => "PS", 'OA' => "OA", 'GA' => "GA", 'EF' => "EF", 'REF' => "REF")))->add('listeerpDateDeclaration')->add('listeerpDateValidAdap')->add('listeerpDelaiAdap')->add('listeerpIdIgn')->add('listeerpSiret')->add('listeerpNumeroVoie')->add('listeerpNumeroComplement')->add('listeerpComplementVoie')->add('listeerpNomVoie')->add('listeerpAliasNomVoie')->add('listeerpLieuDit')->add('listeerpCodePostal')->add('listeerpCodeInsee')->add('listeerpNomCommune')->add('listeerpDepartement')->add('listeerpStatut')->add('Enregistrer', SubmitType::class, array('attr' => array('class' => 'save')));
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
