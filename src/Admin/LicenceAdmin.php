<?php

namespace App\Admin;

use App\Entity\Personnel;
use App\Entity\Saison;
use App\Entity\Licence;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\DateTimeFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\Form\Validator\ErrorElement;
use Sonata\Form\Type\DatePickerType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class LicenceAdmin extends AbstractAdmin
{

    private $ts;
    private $container;

    public function __construct(TokenStorageInterface $ts, ContainerInterface $containerInterface)
    {
        $this->ts = $ts;
        $this->container = $containerInterface;
    }

    public function toString(object $object): string
    {
        return $object instanceof Licence
            ? 'Licence ' . $object->getPersonnel()
            : 'Licence'; // shown in the breadcrumb on the create view
    }

    protected function prePersist(object $object): void
    {
        $user = $this->ts->getToken()->getUser();
        $object->setDateCreated(new \DateTimeImmutable());
        $object->setUserCreated($user);
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('dateElaboration')
                ->assertNotBlank()
            ->end();
    }

    protected function configureFormFields(FormMapper $form): void
    {

        $form->tab("Affiliation")
            ->with("Details", ['class' => 'col-md-8'])
                ->add('dateElaboration', DatePickerType::class, array('label' => 'Date de creation', 'required' => true))
                ->add('status', ChoiceType::class, array('choices'=> Licence::getStatusCodes(),'label' => 'Status', 'required' => true))
                ->add('personnel', ModelAutocompleteType::class, [
                    'label'        => 'Personne',
                    'required'     => true,
                    'property'     => 'full_text',
                    'by_reference' => false,
                    'to_string_callback' => function($entity, $property) {
                        return $entity->getFullName();
                    },
                ])
                ->add('saison', ModelAutocompleteType::class, [
                    'label'        => 'Saison sportive',
                    'required'     => true,
                    'property'     => 'nom',
                    'by_reference' => false,
                ])
                ->add('note', TextareaType::class, array('label' => 'Note ou Remarque', 'required' => true))
            ->end()
        ->end();
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('id', null, ['label'=>'ID'])
            ->add('saison', null, ['label'=>'Saison sportive'])
            ->add('personnel.fullName', null, ['label'=>'Club'])
            ->add('dateElaboration', null, ['label'=>'Signe le'])
            ->add('status', FieldDescriptionInterface::TYPE_STRING, array('template' => '@SonataAdmin/CRUD/list_status_field.html.twig'))
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

        $show->tab("Licence")
            ->with("Details", ['class' => 'col-md-8'])
                ->add('dateElaboration', null, array('label' => 'Date de signature'))
                ->add('saison', null, ['label' => "Saison"])
                ->add('personnel', null, ['label' => "Personnel"])
                ->add('note', null, array('label' => 'Note ou remarques'))
                ->add('status', null, ['label' => "Status"])
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
        $sortValues[DatagridInterface::SORT_BY] = 'dateElaboration';
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('dateElaboration', DateTimeFilter::class, array('label' => 'Date de delivrance'))
            ->add('personnel', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'label' => 'Personne',
                'field_options' => ['property'=>'full_text'],
            ])
            ->add('saison', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'label' => 'Saison',
                'field_options' => ['property'=>'nom'],
            ])
            ->add('status', ChoiceFilter::class, [
                'label' => 'Status',
                'field_type' => ChoiceType::class,
                'field_options' => [
                    'choices' => Licence::getStatusCodes(),
                ],
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