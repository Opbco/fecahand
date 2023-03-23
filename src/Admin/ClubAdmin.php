<?php
// src/Admin/CategoryAdmin.php

namespace App\Admin;

use App\Entity\Club;
use Knp\Menu\ItemInterface;
use Oh\GoogleMapFormTypeBundle\Form\Type\GoogleMapType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class ClubAdmin extends AbstractAdmin
{
    private $ts;
    private $container;

    public function __construct(TokenStorageInterface $ts, ContainerInterface $container)
    {
        $this->ts = $ts;
        $this->container = $container;
    }

    public function toString(object $object): string
    {
        return $object instanceof Club
            ? $object->getNom()
            : 'Club'; // shown in the breadcrumb on the create view
    }

    protected function prePersist(object $object): void
    {
        $fileUploader = $this->container->get('App\Service\FileUploader');
        $user = $this->ts->getToken()->getUser();
        $object->setDateCreated(new \DateTimeImmutable());
        $object->setDateUpdated(new \DateTimeImmutable());
        $object->setUserUpdated($user);
        $object->setUserCreated($user);
        if ($object->getImageFile()) {
            $object->setImageNom($fileUploader->upload($object->getImageFile()));
        }
        if ($object->getPdfFile()) {
            $object->setPdfNom($fileUploader->upload($object->getPdfFile(), 2));
        }
    }

    protected function preUpdate(object $object): void
    {
        $fileUploader = $this->container->get('App\Service\FileUploader');
        $user = $this->ts->getToken()->getUser();
        $object->setDateUpdated(new \DateTimeImmutable());
        $object->setUserUpdated($user);
        if ($object->getImageFile()) {
            $object->setImageNom($fileUploader->upload($object->getImageFile()));
        }
        if ($object->getPdfFile()) {
            $object->setPdfNom($fileUploader->upload($object->getPdfFile(), 2));
        }
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

        $club = $this->getSubject();

        // use $fileFormOptions so we can add other options to the field
        $fileFormOptions = ['required' => false];
        if ($club && ($webPath = $club->getImageWebPath())) {
            $fullPath = $club->getImageAbsolutePath() . $webPath;
            // add a 'help' option containing the preview's img tag
            $fileFormOptions['help'] = '<img style="max-width:100%; width:200px; aspect-ratio:1; object-fit:contain;" src="' . $fullPath . '" class="admin-preview"/>';
            $fileFormOptions['help_html'] = true;
            $fileFormOptions['label'] = 'Logo';
        }

        $fileFormFOptions = ['required' => false];

        if ($club && ($webPath = $club->getPdfWebPath())) {
            $fullPath = $club->getPdfAbsolutePath();
            // add a 'help' option containing the preview's img tag
            $fileFormFOptions['help'] = is_file($fullPath) ? '<a href="' . $webPath . '">Click to download</a>' : 'copie mumerique non disponible';
            $fileFormFOptions['help_html'] = true;
        }

        $form->tab('Club')
                ->with('Informations', ['class' => 'col-md-6'])
                    ->add('imageFile', FileType::class, $fileFormOptions)
                    ->add('nom', TextType::class)
                    ->add('status', ChoiceType::class, array('choices'=> Club::getGenreCodes(), 'label' => 'Genre', 'required' => true))
                    ->add('dateCreation', DateType::class, ['label'=>'Date de creation'])
                    ->add('couleurs', CollectionType::class, [
                        'entry_type' => ColorType::class,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'entry_options' => [
                            'help' => 'Vous pouvez entrez au tant que voulu.',
                            'html5' => true,
                        ],
                        'required' => true
                    ])
                    ->add('devise', TextType::class, array('label' => 'Devise', 'required' => true))
                    ->add('numeroMinat', TextType::class, array('label' => 'Numero MINAT', 'required' => true))
                    ->add('datePublication', DateType::class, ['label'=>'Date de publication'])
                    ->add('pdfFile', FileType::class, $fileFormFOptions)
                ->end()
                ->with('Localisation', ['class' => 'col-md-6'])
                    ->add('latlng', GoogleMapType::class)
                ->end()
            ->end();
         }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('nom', null, ['label'=>'nom'])
            ->add('couleurs', FieldDescriptionInterface::TYPE_ARRAY, [
                'template' => '@SonataAdmin/CRUD/list_color.html.twig',
            ])
            ->add('devise', null, ['label'=>'Devise'])
            ->add('genre', FieldDescriptionInterface::TYPE_STRING, array('template' => '@SonataAdmin/CRUD/list_status_field.html.twig'))
            ->add('dateCreation', null, ['label'=>'Devise'])
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
        $club = $this->getSubject();

        // use $fileFormOptions so we can add other options to the field
        $fileFormOptions['label'] = 'Logo';
        if ($club && $club->getImageAbsolutePath()) {
            $fileFormOptions['template'] = '@SonataAdmin/CRUD/base_show_image.html.twig';
        }

        $show->tab('Club')
                ->with('Informations', ['class' => 'col-md-6'])
                    ->add('imageFileFromName', 'file', $fileFormOptions)
                    ->add('nom', null, ['label' => 'Nom'])
                    ->add('genre', null, ['label' => "Gemre"])
                    ->add('dateCreation', null, ['label'=>'Date de creation'])
                    ->add('couleurs')
                    ->add('devise', null, array('label' => 'Devise'))
                    ->add('numeroMinat', null, array('label' => 'Numero MINAT'))
                    ->add('datePublication',null, ['label'=>'Date de publication'])
                    ->add('pdfFileFromName', 'file', ['label'=>'Copie electronique'])
                ->end()
                ->with('Localisation', ['class' => 'col-md-6'])
                    ->add('address', null, ['label' => 'Adresse'])
                    ->add('latitude', null, ['label' => 'Latitude'])
                    ->add('longitude', null, ['label' => 'Longitude'])
                    ->add('status', null, array('label' => 'Statut'))
                ->end()
            ->end();
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        // display the first page (default = 1)
        $sortValues[DatagridInterface::PAGE] = 1;

        // reverse order (default = 'ASC')
        $sortValues[DatagridInterface::SORT_ORDER] = 'ASC';

        // name of the ordered field (default = the model's id field, if any)
        $sortValues[DatagridInterface::SORT_BY] = 'nom';
    }

   protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('nom', null, array('label' => 'Nom'))
        ->add('dateCreation', null, array('label' => 'date de creation'))
        ->add('address', null, array('label' => 'Adresse'));
    }

    protected function configureTabMenu(ItemInterface $menu, string $action, ?AdminInterface $childAdmin = null): void
    {
        if (!$childAdmin && !in_array($action, ['edit', 'show'])) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;
        $id = $admin->getRequest()->get('id');

        $menu->addChild('Voir', $admin->generateMenuUrl('show', ['id' => $id]));

        if ($this->isGranted('EDIT')) {
            $menu->addChild('Modifier', $admin->generateMenuUrl('edit', ['id' => $id]));
        }

        if ($this->isGranted('LIST')) {
            $menu->addChild('Stades', $admin->generateMenuUrl('admin.clubstade.list', ['id' => $id]));
            $menu->addChild('Affiliations', $admin->generateMenuUrl('admin.affiliation.list', ['id' => $id]));
            $menu->addChild('Assurances', $admin->generateMenuUrl('admin.insurance.list', ['id' => $id]));
        }
    }
}