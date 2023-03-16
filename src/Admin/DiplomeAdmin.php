<?php

namespace App\Admin;

use App\Entity\Diplome;
use App\Entity\Personnel;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\DateTimeFilter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\Form\Validator\ErrorElement;
use Sonata\Form\Type\DatePickerType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;

final class DiplomeAdmin extends AbstractAdmin
{
    private $container;

    public function __construct(ContainerInterface $containerInterface)
    {
        $this->container = $containerInterface;
    }

    public function toString(object $object): string
    {
        return $object instanceof Diplome
            ? 'Diplome ' . $object->getNom().' '.$object->getInstitution()
            : 'Diplome'; // shown in the breadcrumb on the create view
    }

    protected function prePersist(object $object): void
    {
        $fileUploader = $this->container->get('App\Service\FileUploader');
        if ($object->getPdfFile()) {
            $object->setPdfNom($fileUploader->upload($object->getPdfFile(), 2));
        }
    }

    protected function preUpdate(object $object): void
    {
        $this->prePersist($object);
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('nom')
            ->assertNotBlank()
            ->assertNotNull()
            ->end();
    }

    protected function configureFormFields(FormMapper $form): void
    {

        $diplome = $this->getSubject();

        // use $fileFormOptions so we can add other options to the field
        $fileFormFOptions = ['required' => false];

        if ($diplome && ($webPath = $diplome->getPdfAbsolutePath())) {
            // get the request so the full path to the image can be set
            $request = $this->getRequest();
            $fullPath = $diplome->getPdfAbsolutePath();
            // add a 'help' option containing the preview's img tag
            $fileFormFOptions['help'] = is_file($fullPath) ? '<a href="' . $webPath . '">Click to download</a>' : 'copie mumerique non disponible';
            $fileFormFOptions['help_html'] = true;
        }

        $form->tab('Diplome')
                ->with("Details", ['class' => 'col-md-6'])
                    ->add('nom', TextType::class, array('label' => 'Nom du diplome', 'required' => true))
                    ->add('deliveryDate', DatePickerType::class, array('label' => 'Delivre le', 'required' => true))
                    ->add('institution', TextType::class, array('label' => 'Delivre par', 'required' => true))
                    ->add('personne', ModelListType::class, [
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
            ->add('personne.fullName', null, ['label'=>'Nom du concerne'])
            ->add('nom', null, ['label'=>'Nom du diplome'])
            ->add('deliveryDate')
            ->add('institution', null, [
                'editable' => true, 'label' => 'Institution'
            ])
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

        $show->tab('Diplome')
                ->with("Details", ['class' => 'col-md-6'])
                    ->add('personne', null, array('label' => 'Personne'))
                    ->add('nom', null, array('label' => 'Numero'))
                    ->add('deliveryDate', null, array('label' => 'Delivre le'))
                    ->add('institution',null, array('label' => 'Institution')) 
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
        $sortValues[DatagridInterface::SORT_BY] = 'deliveryDate';
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('nom', null, array('label' => 'Nom du diplome'))
            ->add('institution', null, array('label' => 'Institution'))
            ->add('deliveryDate', DateTimeFilter::class, array('label' => 'Date de delivrance'))
            ->add('personne', null, [
                'field_type' => EntityType::class,
                'field_options' => [
                    'class' => Personnel::class,
                    'choice_label' => 'nom',
                ],
            ]);
    }

}
