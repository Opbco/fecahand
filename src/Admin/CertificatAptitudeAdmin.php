<?php

namespace App\Admin;

use App\Entity\CertificatAptitude;
use App\Entity\Diplome;
use App\Entity\Personnel;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\DateTimeFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\Form\Type\BooleanType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\Form\Validator\ErrorElement;
use Sonata\Form\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class CertificatAptitudeAdmin extends AbstractAdmin
{

    public function toString(object $object): string
    {
        return $object instanceof CertificatAptitude
            ? 'Certificat Aptitude' . $object->getId()
            : 'Certificat Aptitude'; // shown in the breadcrumb on the create view
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('deliveryDate')
                ->assertDate()
                ->assertNotNull()
            ->end()
            ->with('deliveryBy')
                ->assertNotBlank()
                ->assertNotNull()
            ->end();
    }

    protected function configureFormFields(FormMapper $form): void
    {

        $form->tab("Certificat d'aptitude")
                ->with("Details", ['class' => 'col-md-8'])
                    ->add('deliveryDate', DatePickerType::class, array('label' => 'Delivre le', 'required' => true))
                    ->add('deliveryBy', TextType::class, array('label' => 'Delivre par', 'required' => true))
                    ->add('deliveryAt', TextType::class, array('label' => 'Delivre a', 'required' => true))
                    ->add('remarks', TextareaType::class, array('label' => 'Remarques', 'required' => true))
                    ->add('personne', ModelAutocompleteType::class, [
                        'property' => 'nom',
                        'to_string_callback' => function($entity, $property) {
                            return $entity->getFullName();
                        },
                    ])
                ->end()
            ->end();
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('id', null, ['label'=>'ID'])
            ->add('personne.fullName', null, ['label'=>'Nom du concerne'])
            ->add('deliveryBy', null, ['label'=>'Delivre Par'])
            ->add('deliveryDate', null, ['label'=>'Delivre le'])
            ->add('deliveryAt', null, ['label' => 'Delivre a'])
            ->add('isValid', null, ['label' => 'Valide'])
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

        $show->tab("Certificat d'aptitude")
                ->with("Details", ['class' => 'col-md-8'])
                    ->add('deliveryDate', null, array('label' => 'Delivre le', 'required' => true))
                    ->add('deliveryBy', null, array('label' => 'Delivre par', 'required' => true))
                    ->add('deliveryAt', null, array('label' => 'Delivre a', 'required' => true))
                    ->add('remarks', null, array('label' => 'Remarques', 'required' => true))
                    ->add('personne', null, array('label' => 'Personne'))
                ->end()
            ->end();
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        // display the first page (default = 1)
        $sortValues[DatagridInterface::PAGE] = 1;

        // reverse order (default = 'ASC')
        $sortValues[DatagridInterface::SORT_ORDER] = 'DESC';

        // name of the ordered field (default = the model's id field, if any)
        $sortValues[DatagridInterface::SORT_BY] = 'deliveryDate';
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('deliveryBy', null, array('label' => 'Delivre par'))
            ->add('deliveryAt', null, array('label' => 'Delivre a'))
            ->add('deliveryDate', DateTimeFilter::class, array('label' => 'Date de delivrance'))
            ->add('personne', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'label' => 'Personne',
                'field_options' => ['class' => Personnel::class, 'property'=>'nom'],
            ]);
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        if ($this->isChild()) {
            return;
        }

        // This is the route configuration as a parent
        $collection->clear();

    }

}
