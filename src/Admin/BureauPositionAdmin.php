<?php

namespace App\Admin;

use App\Entity\BureauPosition;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

final class BureauPositionAdmin extends AbstractAdmin
{
    public function toString(object $object): string
    {
        return $object instanceof BureauPosition
            ? $object->getBureau(). ' '.$object->getPosition()
            : 'Bureau et Position'; // shown in the breadcrumb on the create view
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
            ->add('position', ModelAutocompleteType::class, [
                'label'        => 'Positions',
                'required'     => false,
                'property'     => 'nom',
                'by_reference' => false,
                'to_string_callback' => function($entity, $property) {
                    return $entity->getNom();
                },
            ])
            ->add('nombre', NumberType::class, ['required' => true] )
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('bureau')
            ->add('position')
            ->add('nombre')
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
        $filter->add('bureau', ModelAutocompleteFilter::class, [
            'label' => 'Bureau',
            'field_options' => ['property'=>'nom'],
        ])
            ->add('position', ModelAutocompleteFilter::class, [
                'label' => 'Position',
                'field_options' => ['property'=>'nom'],
            ])
            ->add('nombre', null, ['label'=>'Nombre de poste']);
        
    }

}