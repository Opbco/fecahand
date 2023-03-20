<?php

namespace App\Admin;

use App\Entity\Club;
use App\Entity\Affiliation;
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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class AffiliationAdmin extends AbstractAdmin
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
        return $object instanceof Affiliation
            ? 'Affiliation ' . $object->getClub()
            : 'Affiliation'; // shown in the breadcrumb on the create view
    }

    protected function prePersist(object $object): void
    {
        $user = $this->ts->getToken()->getUser();
        $object->setDateCreated(new \DateTimeImmutable());
        $object->setUserCreated($user);
    }

    protected function preUpdate(object $object): void
    {
        $user = $this->ts->getToken()->getUser();
        $object->setDateUpdated(new \DateTimeImmutable());
        $object->setUserUpdated($user);
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('dateAffiliation')
                ->assertNotBlank()
            ->end();
    }

    protected function configureFormFields(FormMapper $form): void
    {

        $form->tab("Affiliation")
            ->with("Details", ['class' => 'col-md-8'])
                ->add('dateAffiliation', DatePickerType::class, array('label' => 'Date de creation', 'required' => true))
                ->add('status', ChoiceType::class, array('choices'=> Affiliation::getStatusCodes(),'label' => 'Status', 'required' => true))
                ->add('club', ModelAutocompleteType::class, [
                    'label'        => 'Club',
                    'required'     => true,
                    'property'     => 'nom',
                    'by_reference' => false,
                ])
            ->end()
        ->end();
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('id', null, ['label'=>'ID'])
            ->add('club', null, ['label'=>'Club'])
            ->add('dateAffiliation', null, ['label'=>'Signe le'])
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

        $show->tab("Affiliation")
            ->with("Details", ['class' => 'col-md-8'])
                ->add('dateAffiliation', null, array('label' => 'Date de signature'))
                ->add('club', null, ['label' => "Club"])
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
        $sortValues[DatagridInterface::SORT_BY] = 'dateAffiliation';
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('dateAffiliation', DateTimeFilter::class, array('label' => 'Date de delivrance'))
            ->add('club', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'label' => 'Club',
                'field_options' => ['property'=>'nom'],
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