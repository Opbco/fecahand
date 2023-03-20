<?php

namespace App\Admin;

use App\Entity\Club;
use App\Entity\ClubStade;
use App\Entity\Stade;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;

final class ClubStadeAdmin extends AbstractAdmin
{

    public function toString(object $object): string
    {
        return $object instanceof ClubStade
            ? $object->getClub() . ' ' . $object->getStade()
            : 'Club et Stade'; // shown in the breadcrumb on the create view
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('club', ModelAutocompleteType::class, [
                'label'        => 'Club',
                'required'     => true,
                'property'     => 'nom',
                'by_reference' => false,
            ])
            ->add('stade', ModelAutocompleteType::class, [
                'label'        => 'Stade',
                'required'     => true,
                'property'     => 'nom',
                'by_reference' => false,
            ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->add('club')
            ->add('stade')
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
        $filter->add('club', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'label' => 'Club',
                'field_options' => ['class' => Club::class, 'property' => 'nom'],
            ])
            ->add('stade', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'label' => 'Stade',
                'field_options' => ['class' => Stade::class, 'property' => 'nom'],
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
