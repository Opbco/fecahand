<?php
// src/Admin/CategoryAdmin.php

namespace App\Admin;

use App\Entity\Stade;
use App\Form\Type\DimensionType;
use Oh\GoogleMapFormTypeBundle\Form\Type\GoogleMapType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\StringListFilter;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class StadeAdmin extends AbstractAdmin
{
    private $ts;

    public function __construct(TokenStorageInterface $ts)
    {
        $this->ts = $ts;
    }

    public function toString(object $object): string
    {
        return $object instanceof Stade
            ? $object->getNom()
            : 'Stade'; // shown in the breadcrumb on the create view
    }

    protected function prePersist(object $object): void
    {
        $user = $this->ts->getToken()->getUser();
        $object->setUserCreated($user);
    }

    protected function preUpdate(object $object): void
    {
        $this->prePersist($object);
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('nom')
            ->assertNotBlank()
            ->assertNotNull()
            ->end();
    }

    protected function configureFormFields(FormMapper $form): void
    {

        $form->tab('Details')
                ->with('Informations', ['class' => 'col-md-6'])
                    ->add('nom', TextType::class)
                    ->add('dimension', DimensionType::class, ['label'=>'Dimensions'])
                    ->add('encercle', ChoiceType::class, array('choices'=> [
                        'Choisir' => '',
                        'oui' => true,
                        'non' => false,
                    ],'label' => 'Est-il encercle?', 'required' => true))
                    ->add('comment', TextareaType::class, array('label' => 'Commentaire', 'required' => true))
                ->end()
                ->with('Localisation', ['class' => 'col-md-6'])
                    ->add('latlng', GoogleMapType::class)
                ->end()
            ->end();
         }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('nom', null, ['label'=>'nom'])
            ->add('dimension', FieldDescriptionInterface::TYPE_ARRAY, [
                'inline' => true,
                'display' => 'both',
                'key_translation_domain' => true,
                'value_translation_domain' => null
            ])
            ->add('Comment', null, ['label'=>'Position'])
            ->add('encercle', null, [
                'editable' => true, 'label' => 'Est-il encercle?'
            ])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [
                        'link_parameters' => [
                            'full' => true,
                        ]
                    ],
                ],
                'row_align' => 'right'
            ]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {

        $show->tab('Stade')
                ->with('Informations', ['class' => 'col-md-6'])
                    ->add('nom', null, ['label' => 'Nom'])
                    ->add('dimension', null, ['label' => 'Dimensions (m) '])
                    ->add('encercle', null, ['label' => 'Encercle'])
                    ->add('comment', null, ['label' => 'Commentaire'])
                ->end()
                ->with('Localisation', ['class' => 'col-md-6'])
                    ->add('address', null, ['label' => 'Adresse'])
                    ->add('latitude', null, ['label' => 'Latitude'])
                    ->add('longitude', null, ['label' => 'Longitude'])
                    ->add('status', null, array('label' => 'Statut'))
                ->end()
            ->end();
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        // display the first page (default = 1)
        $sortValues[DatagridInterface::PAGE] = 1;

        // reverse order (default = 'ASC')
        $sortValues[DatagridInterface::SORT_ORDER] = 'ASC';

        // name of the ordered field (default = the model's id field, if any)
        $sortValues[DatagridInterface::SORT_BY] = 'nom';
    }

   protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('nom', null, array('label' => 'Nom'))
        ->add('encercle', null, array('label' => 'Est-il encercle?'))
        ->add('dimension', StringListFilter::class, [
            'field_type' => DimensionType::class,
        ])
        ->add('address', null, array('label' => 'Adresse'));
    }
}