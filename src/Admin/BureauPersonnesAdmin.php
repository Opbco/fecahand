<?php

namespace App\Admin;

use App\Entity\Bureau;
use App\Entity\BureauPersonnes;
use App\Entity\Personnel;
use App\Entity\Position;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

final class BureauPersonnesAdmin extends AbstractAdmin
{
    public function toString(object $object): string
    {
        return $object instanceof BureauPersonnes
            ? $object->getBureau(). ' '.$object->getPersonne()
            : 'Bureau et Personnes'; // shown in the breadcrumb on the create view
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('bureau', ModelAutocompleteType::class, [
                'label'        => 'Bureau',
                'required'     => true,
                'property'     => 'nom',
                'by_reference' => false,
                'to_string_callback' => function($entity, $property) {
                    return $entity->getNom();
                },
            ])
            ->add('personne', ModelAutocompleteType::class, [
                'label'        => 'Personne',
                'required'     => true,
                'property'     => 'full_text',
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
        $list
            ->add('bureau')
            ->add('personne.fullName')
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
        $filter->add('bureau', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'label' => 'Bureau',
                'field_options' => ['class' => Bureau::class, 'property' => 'nom'],
            ])
            ->add('position', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'label' => 'Position',
                'field_options' => ['class' => Position::class, 'property' => 'nom'],
            ])
            ->add('personne', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'label' => 'Personne',
                'field_options' => ['class' => Personnel::class, 'property' => 'full_text'],
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