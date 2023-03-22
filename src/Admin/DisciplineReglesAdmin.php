<?php

namespace App\Admin;

use App\Entity\DisciplineAffinitaire;
use App\Entity\DisciplineRegles;
use App\Entity\Regle;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;

final class DisciplineReglesAdmin extends AbstractAdmin
{

    public function toString(object $object): string
    {
        return $object instanceof DisciplineRegles
            ? $object->getDiscipline(). ' '.$object->getRegle()
            : 'Discipline et Regle'; // shown in the breadcrumb on the create view
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('discipline', ModelAutocompleteType::class, [
                'label'        => 'Discipline affinitaire',
                'required'     => true,
                'property'     => 'nom',
                'by_reference' => false,
                'to_string_callback' => function($entity, $property) {
                    return $entity->getNom();
                },
            ])
            ->add('regle', ModelAutocompleteType::class, [
                'label'        => 'Regle',
                'required'     => true,
                'property'     => 'nom',
                'by_reference' => false,
                'to_string_callback' => function($entity, $property) {
                    return $entity->getNom();
                },
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->add('discipline', null, ['label'=>'Regles'])
             ->add('regle', null, ['label'=>'Regles'])
             ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'delete' => [],
                    'edit' => [
                        'link_parameters' => [
                            'full' => true,
                        ]
                    ],
                ],
                'row_align' => 'right'
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add('discipline', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'label' => 'Discipline',
                'field_options' => ['class' => DisciplineAffinitaire::class, 'property' => 'nom'],
            ])
            ->add('regle', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'label' => 'Regle',
                'field_options' => ['class' => Regle::class, 'property' => 'nom'],
            ]);
        
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        if ($this->isChild()) {
            return;
        }

        // This is the route configuration as a parent
        $collection->clearExcept(['list']);
    }
}