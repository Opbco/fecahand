<?php

namespace App\Admin;

use App\Entity\Insurance;
use App\Entity\Club;
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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\Form\Validator\ErrorElement;
use Sonata\Form\Type\DatePickerType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class InsuranceAdmin extends AbstractAdmin
{

    private $ts;

    private $container;

    public function __construct(TokenStorageInterface $ts, ContainerInterface $containerInterface)
    {
        $this->container = $containerInterface;
        $this->ts = $ts;
    }

    public function toString(object $object): string
    {
        return $object instanceof Insurance
            ? 'Assurance ' . $object->getNumero()
            : 'Assurance'; // shown in the breadcrumb on the create view
    }

    protected function prePersist(object $object): void
    {
        $fileUploader = $this->container->get('App\Service\FileUploader');
        if ($object->getPdfFile()) {
            $object->setPdfNom($fileUploader->upload($object->getPdfFile(), 2));
        }
        $user = $this->ts->getToken()->getUser();
        $object->setDateCreated(new \DateTimeImmutable());
        $object->setStatus(1);
        $object->setUserCreated($user);
    }

    protected function preUpdate(object $object): void
    {
        $fileUploader = $this->container->get('App\Service\FileUploader');
        if ($object->getPdfFile()) {
            $object->setPdfNom($fileUploader->upload($object->getPdfFile(), 2));
        }
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('numero')
                ->assertNotBlank()
            ->end()
            ->with('deliveredBy')
                ->assertNotBlank()
            ->end();
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $insurance = $this->getSubject();

        // use $fileFormOptions so we can add other options to the field
        $fileFormFOptions = ['required' => false];

        if ($insurance && ($webPath = $insurance->getPdfWebPath())) {
            // get the request so the full path to the image can be set
            $fullPath = $insurance->getPdfAbsolutePath();
            // add a 'help' option containing the preview's img tag
            $fileFormFOptions['help'] = is_file($fullPath) ? '<a href="' . $webPath . '">Click to download</a>' : 'copie mumerique non disponible';
            $fileFormFOptions['help_html'] = true;
        }

        $form->tab("Assurance")
                ->with("Details", ['class' => 'col-md-8'])
                    ->add('numero', TextType::class, array('label' => 'Numero', 'required' => true))
                    ->add('dateDelivrance', DatePickerType::class, array('label' => 'Delivre le', 'required' => true))
                    ->add('dateExpiration', DatePickerType::class, array('label' => 'Expire le', 'required' => true))
                    ->add('deliveredBy', TextType::class, array('label' => 'Delivre par', 'required' => true))
                    ->add('deliveredAt', TextType::class, array('label' => 'Delivre a', 'required' => true))
                    ->add('typeAssMul', ChoiceType::class, [
                        'choices' => [
                            'Assurance voiture' => 'voiture',
                            'Assurance habitation' => 'habitation',
                            'Assurance obsèques' => 'obseque',
                            'Assurance responsabilité civile' => 'responciv',
                            'Assurance individuelle accident' => 'indivacc'
                        ],
                        'multiple' => true,
                        'choice_translation_domain' => 'App',
                        'label' => "Type d'assurance"
                    ])
                    ->add('club', ModelListType::class, [
                        'class' => Personnel::class,
                        'label' => "Personne concerne",
                        'btn_delete' => false,
                        ])
                    ->add('pdfFile', FileType::class, $fileFormFOptions)
                ->end()
            ->end();
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('id', null, ['label'=>'ID'])
            ->add('club.nom', null, ['label'=>'Nom du club'])
            ->add('deliveredBy', null, ['label'=>'Delivre Par'])
            ->add('dateDelivrance', null, ['label'=>'Delivre le'])
            ->add('dateExpiration', null, ['label'=>'Expire le'])
            ->add('deliveredAt', null, ['label' => 'Delivre a'])
            ->add('status', null, ['label' => 'Statut'])
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
                ->add('numero', null, array('label' => 'Numero', 'required' => true))
                ->add('dateDelivrance', null, array('label' => 'Delivre le', 'required' => true))
                ->add('dateExpiration', null, array('label' => 'Expire le', 'required' => true))
                ->add('deliveredBy', null, array('label' => 'Delivre par'))
                ->add('deliveredAt', null, array('label' => 'Delivre a'))
                ->add('typeAssurance', null, ['label' => "Type d'assurance"])
                ->add('club', null, ['label' => "Personne concerne"])
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
        $sortValues[DatagridInterface::SORT_BY] = 'dateExpiration';
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('deliveredBy', null, array('label' => 'Delivre par'))
            ->add('deliveredAt', null, array('label' => 'Delivre a'))
            ->add('dateDelivrance', DateTimeFilter::class, array('label' => 'Date de delivrance'))
            ->add('dateExpiration', DateTimeFilter::class, array('label' => 'Date expiration'))
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
