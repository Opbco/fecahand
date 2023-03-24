<?php

namespace App\Admin;

use App\Entity\Club;
use App\Entity\Insurance;
use App\Entity\Personnel;
use Doctrine\DBAL\Types\FloatType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\BooleanFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateTimeFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\Form\Validator\ErrorElement;
use Sonata\Form\Type\DatePickerType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class ContratAdmin extends AbstractAdmin
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
        return $object instanceof Insurance
            ? 'Contrat ' . $object->getClub()
            : 'Contrat'; // shown in the breadcrumb on the create view
    }

    protected function prePersist(object $object): void
    {
        $fileUploader = $this->container->get('App\Service\FileUploader');
        $user = $this->ts->getToken()->getUser();
        $object->setDateCreated(new \DateTimeImmutable());
        $object->setUserCreated($user);
        if ($object->getPdfFile()) {
            $object->setPdfNom($fileUploader->upload($object->getPdfFile(), 2));
        }
    }

    protected function preUpdate(object $object): void
    {
        $user = $this->ts->getToken()->getUser();
        $fileUploader = $this->container->get('App\Service\FileUploader');
        $object->setDateUpdated(new \DateTimeImmutable());
        $object->setUserUpdated($user);
        if ($object->getPdfFile()) {
            $object->setPdfNom($fileUploader->upload($object->getPdfFile(), 2));
        }
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('dateSignature')
                ->assertNotBlank()
            ->end()
            ->with('dateFin')
                ->assertNotBlank()
            ->end();
    }

    protected function configureFormFields(FormMapper $form): void
    {
            
        $contrat = $this->getSubject();

        // use $fileFormOptions so we can add other options to the field
        $fileFormFOptions = ['required' => false];

        if ($contrat && ($webPath = $contrat->getPdfWebPath())) {
            // get the request so the full path to the image can be set
            $request = $this->getRequest();
            $fullPath = $contrat->getPdfAbsolutePath();
            // add a 'help' option containing the preview's img tag
            $fileFormFOptions['help'] = is_file($fullPath) ? '<a href="' . $webPath . '">Click to download</a>' : 'copie mumerique non disponible';
            $fileFormFOptions['help_html'] = true;
        }

        $form->tab("Contrat")
            ->with("Details", ['class' => 'col-md-8'])
                ->add('dateSignature', DatePickerType::class, array('label' => 'Date de signature', 'required' => true))
                ->add('dateFin', DatePickerType::class, array('label' => 'Date de fin', 'required' => true))
                ->add('salaire', NumberType::class, ['label' => 'Salaire mensuel','required' => true] )
                ->add('renouvellable', ChoiceType::class, array('choices'=> [
                    'Choisir' => '',
                    'oui' => true,
                    'non' => false,
                ],'label' => 'Renouvelable ?', 'required' => true))
                ->add('club', ModelAutocompleteType::class, [
                    'label'        => 'Club',
                    'required'     => true,
                    'property'     => 'nom',
                    'by_reference' => false,
                ])
                ->add('personnel', ModelAutocompleteType::class, [
                    'label'        => 'Personne',
                    'required'     => true,
                    'property'     => 'nom',
                    'by_reference' => false,
                    'to_string_callback' => function($entity, $property) {
                        return $entity->getFullName();
                    },
                ])
                ->add('pdfFile', FileType::class, $fileFormFOptions)
            ->end()
        ->end();
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('id', null, ['label'=>'ID'])
            ->add('personnel.fullName', null, ['label'=>'Nom du concerne'])
            ->add('club', null, ['label'=>'Club'])
            ->add('dateSignature', null, ['label'=>'Signe le'])
            ->add('dateFin', null, ['label'=>'Expire le'])
            ->add('salaire', FieldDescriptionInterface::TYPE_FLOAT, ['label' => 'Salaire Mensuel'])
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

        $show->tab("Assurance")
            ->with("Details", ['class' => 'col-md-8'])
                ->add('dateSignature', null, array('label' => 'Date de signature'))
                ->add('dateFin', null, array('label' => 'Date de fin'))
                ->add('salaire', null, ['label' => 'Salaire mensuel'] )
                ->add('renouvellable', null, array('label' => 'Renouvelable ?'))
                ->add('club', null, ['label' => "Club"])
                ->add('personnel', null, ['label' => "Personne concerne"])
                ->add('pdfFileFromName', 'file', ['label'=>'Copie electronique'])
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
        $sortValues[DatagridInterface::SORT_BY] = 'dateFin';
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('salaire', null, array('label' => 'Salaire'))
            ->add('dateSignature', DateTimeFilter::class, array('label' => 'Date de delivrance'))
            ->add('dateFin', DateTimeFilter::class, array('label' => 'Date expiration'))
            ->add('renouvellable', BooleanFilter::class, array('label' => 'Renouvellable'))
            ->add('personnel', ModelFilter::class, [
                'field_type' => ModelAutocompleteType::class,
                'label' => 'Personne',
                'field_options' => ['property'=>'nom'],
            ])
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
