<?php

namespace App\Admin;

use App\Entity\PersonnelPosition;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;

final class PersonnelPositionAdmin extends AbstractAdmin
{

    public function toString(object $object): string
    {
        return $object instanceof PersonnelPosition
            ? $object->getPersonnel(). ' '.$object->getPosition()
            : 'Personne et Position'; // shown in the breadcrumb on the create view
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('personnel', ModelAutocompleteType::class, [
                'label'        => 'Personnel',
                'required'     => true,
                'property'     => 'nom',
                'by_reference' => false,
                'to_string_callback' => function($entity, $property) {
                    return $entity->getFullName();
                },
            ])
            ->add('position', ModelAutocompleteType::class, [
                'label'        => 'Positions',
                'required'     => false,
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
        $list->add('personnel')
             ->add('position')
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
        $filter->add('personnel', ModelAutocompleteFilter::class, [
            'label' => 'Personne',
            'field_options' => ['property'=>'nom'],
        ])
            ->add('position', ModelAutocompleteFilter::class, [
                'label' => 'Position',
                'field_options' => ['property'=>'nom'],
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