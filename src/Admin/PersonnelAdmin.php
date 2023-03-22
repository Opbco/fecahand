<?php
// src/Admin/CategoryAdmin.php

namespace App\Admin;

use App\Entity\Personnel;
use App\Entity\User;
use Knp\Menu\ItemInterface;
use Oh\GoogleMapFormTypeBundle\Form\Type\GoogleMapType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface as DatagridProxyQueryInterface;
use Sonata\AdminBundle\Filter\Model\FilterData;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateTimeFilter;
use Sonata\Form\Type\BooleanType;
use Sonata\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Sonata\Form\Validator\ErrorElement;
use Sonata\Form\Type\DatePickerType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class PersonnelAdmin extends AbstractAdmin
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
        return $object instanceof Personnel
            ? $object->getFullName()
            : 'Personne'; // shown in the breadcrumb on the create view
    }

    protected function prePersist(object $object): void
    {
        $fileUploader = $this->container->get('App\Service\FileUploader');
        $user = $this->ts->getToken()->getUser();
        $object->setDateCreated(new \DateTimeImmutable());
        $object->setUserCreated($user);
        if ($object->getImageFile()) {
            $object->setAvatarNomFichier($fileUploader->upload($object->getImageFile()));
        }
        if ($object->getCniScanFile()) {
            $object->setCniScanFileName($fileUploader->upload($object->getCniScanFile(), 2));
        }
    }

    protected function postRemove(object $object): void
    {
        $object->removeAvatarFile();
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

        $personne = $this->getSubject();

        // use $fileFormOptions so we can add other options to the field
        $fileFormOptions = ['required' => false];
        $fileFormFOptions = ['required' => false];
        if ($personne && ($webPath = $personne->getAvatarWebPath())) {
            // get the request so the full path to the image can be set
            $request = $this->getRequest();
            $fullPath = $request->getBaseUrl() . $webPath;
            // add a 'help' option containing the preview's img tag
            $fileFormOptions['help'] = '<img style="max-width:100%; width:200px; aspect-ratio:1; object-fit:contain;" src="' . $fullPath . '" class="admin-preview"/>';
            $fileFormOptions['help_html'] = true;
        }

        if ($personne && ($webPath = $personne->getCniFileWebPath())) {
            // get the request so the full path to the image can be set
            $request = $this->getRequest();
            $fullPath = $personne->getCniFileAbsolutePath();
            // add a 'help' option containing the preview's img tag
            $fileFormFOptions['help'] = is_file($fullPath) ? '<a href="' . $webPath . '">Copie scannee de la piece</a>' : 'copie piece non disponible';
            $fileFormFOptions['help_html'] = true;
        }

        $form->tab('Personne')
            ->with('Informations personnelles', ['class' => 'col-md-6'])
            ->add('imageFile', FileType::class, $fileFormOptions)
            ->add('nom', TextType::class)
            ->add('prenoms', TextType::class)
            ->add('genre', ChoiceType::class, [
                'choices' => [
                    'Masculin' => 'Masculin',
                    'Feminin' => 'Feminin',
                ],
                'label' => 'Choose the gender',
                'required' => true
            ])
            ->add('dateNaiss', DatePickerType::class, [
                'label' => 'Date de naissance',
                'required' => true
            ])
            ->add('lieuNaiss', TextType::class, array('label' => 'Lieu de naissance', 'required' => true))
            ->add('profession', TextType::class, array('label' => 'Profession', 'required' => false))
            ->add('allergies', TextType::class, array('label' => 'Allergies', 'required' => false, 'help'=>'Separer chaque allergie par une virgule'))
            ->add('groupeSangin', ChoiceType::class, [
                'choices'  => [
                    'A+' => 'A+',
                    'A-' => 'A-',
                    'B-' => 'B-',
                    'B+' => 'B+',
                    'O-' => 'O-',
                    'O+' => 'O+',
                    'AB-' => 'AB-',
                    'AB+' => 'AB+',
                ],
                'label' => 'Groupe sanguin',
                'required' => false
                ])
                ->add('phoneMobile', NumberType::class, array('label' => 'Tel Mobile', 'required' => false))
                ->add('phoneWhatsapp', NumberType::class, array('label' => 'Tel WhatsApp', 'required' => false))
                ->end()
                ->with("Piece d'identite", ['class' => 'col-md-6'])
                    ->add('numeroCni', TextType::class, array('label' => 'Numero', 'required' => true))
                    ->add('cniDeliverOn', DatePickerType::class, array('label' => 'Delivre le', 'required' => true))
                    ->add('cniDeliverAt', TextType::class, array('label' => 'Delivre a', 'required' => true))
                    ->add('cniSignedBy', TextType::class, array('label' => 'Signee par', 'required' => true)) 
                    ->add('cniScanFile', FileType::class, $fileFormFOptions)
                ->end()
                ->with('Localisation', ['class' => 'col-md-6'])
                    ->add('latlng', GoogleMapType::class)
                    ->add('account', ModelType::class, [
                        'class' => User::class,
                        'property' => 'username',
                        'label' => "Compte d'utilisateur"
                        ])
                    ->add('status', BooleanType::class, array('label' => 'Actif', 'required' => false))
                ->end()
            ->end()
            ->tab('Personne a contacter')
                ->with('Informations personnelles', ['class' => 'col-md-6'])
                    ->add('personneContactNom', TextType::class, array('label' => 'Nom complet', 'required' => false))
                    ->add('personneContactQualite', TextType::class, array('label' => 'En qualite de', 'required' => false))
                    ->add('personneContactPhone', NumberType::class, array('label' => 'Contacts', 'required' => false))
                    ->add('personneContactAdresse', TextareaType::class, array('label' => 'Adresse', 'required' => false))
                ->end()
            ->end()
            ->tab('Documentation')
                ->with('Diplomes', ['class' => 'col-md-12'])
                    ->add('diplomes', CollectionType::class, [
                        'type_options' => [
                            // Prevents the "Delete" option from being displayed
                            'delete' => true,
                            'delete_options' => [
                                // You may otherwise choose to put the field but hide it
                                'type'         => CheckboxType::class,
                                // In that case, you need to fill in the options as well
                                'type_options' => [
                                    'mapped'   => false,
                                    'required' => false,
                                ]
                            ]
                        ]
                    ], [
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',
                    ])
                ->end()
                ->with('Certificat Aptitude', ['class' => 'col-md-12'])
                    ->add('certificatAptitudes', CollectionType::class, [
                        'type_options' => [
                            // Prevents the "Delete" option from being displayed
                            'delete' => true,
                            'delete_options' => [
                                // You may otherwise choose to put the field but hide it
                                'type'         => CheckboxType::class,
                                // In that case, you need to fill in the options as well
                                'type_options' => [
                                    'mapped'   => false,
                                    'required' => false,
                                ]
                            ]
                        ]
                    ], [
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',
                    ])
                ->end()
                ->with('Assurance', ['class' => 'col-md-12'])
                    ->add('insurances', CollectionType::class, [
                        'type_options' => [
                            // Prevents the "Delete" option from being displayed
                            'delete' => true,
                            'delete_options' => [
                                // You may otherwise choose to put the field but hide it
                                'type'         => CheckboxType::class,
                                // In that case, you need to fill in the options as well
                                'type_options' => [
                                    'mapped'   => false,
                                    'required' => false,
                                ]
                            ]
                        ]
                    ], [
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',
                    ])
                ->end()
            ->end();
         }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('numeroCni', null, ['label'=>'Numero CNI'])
            ->add('fullName', null, ['label'=>'Nom complet'])
            ->add('genre')
            ->add('myPositions', null, ['label'=>'Position'])
            ->add('status', null, [
                'editable' => true, 'label' => 'Actif'
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
        $personne = $this->getSubject();

        // use $fileFormOptions so we can add other options to the field
        if ($personne && ($webPath = $personne->getAvatarWebPath())) {
            $fileFormOptions['label'] = 'Picture';
            $fileFormOptions['template'] = '@SonataAdmin/CRUD/base_show_image.html.twig';
        }

        $show->tab('Personne')
                ->with('Informations personnelles', ['class' => 'col-md-6'])
                    ->add('fileFromName', 'file', $fileFormOptions)
                    ->add('myPositions', null, ['label'=>'Position(s)'])
                    ->add('nom', null, ['label' => 'Nom'])
                    ->add('prenoms', null, ['label' => 'Prenoms'])
                    ->add('genre', null, ['label' => 'Genre'])
                    ->add('dateNaiss', null, ['label' => 'Date de naissance'])
                    ->add('lieuNaiss', null, array('label' => 'Lieu de naissance'))
                    ->add('profession', null, array('label' => 'Profession'))
                    ->add('allergies', null, ['label' => 'Allergies'])
                    ->add('groupeSangin', null, ['label' => 'Groupe sanguin'])
                    ->add('phoneMobile', null, array('label' => 'Tel Mobile'))
                    ->add('phoneWhatsapp', null, array('label' => 'Tel WhatsApp'))
                ->end()
                ->with("Piece d'identite", ['class' => 'col-md-6'])
                    ->add('numeroCni', null, array('label' => 'Numero', 'required' => true))
                    ->add('cniDeliverOn', null, array('label' => 'Delivre le', 'required' => true))
                    ->add('cniDeliverAt', null, array('label' => 'Delivre a', 'required' => true))
                    ->add('cniSignedBy',null, array('label' => 'Signee par', 'required' => true)) 
                    ->add('cniScanFileFromName', 'file', ['label'=>'Copie piece'])
                ->end()
                ->with('Localisation', ['class' => 'col-md-6'])
                    ->add('address', null, ['label' => 'Adresse'])
                    ->add('latitude', null, ['label' => 'Latitude'])
                    ->add('longitude', null, ['label' => 'Longitude'])
                    ->add('account', null, ['label' => "Nom d'utilisateur"])
                    ->add('status', null, array('label' => 'Actif'))
                ->end()
            ->end()
            ->tab('Personne a contacter')
                ->with('Informations', ['class' => 'col-md-6'])
                    ->add('personneContactNom', null, array('label' => 'Nom complet'))
                    ->add('personneContactQualite', null, array('label' => 'En qualite de'))
                    ->add('personneContactPhone', null, array('label' => 'Contacts'))
                    ->add('personneContactAdresse', null, array('label' => 'Adresse'))
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
        $sortValues[DatagridInterface::SORT_BY] = 'prenoms';
    }

   protected function configureQuery(DatagridProxyQueryInterface $query): DatagridProxyQueryInterface
   {
        $rootAlias = current($query->getRootAliases());

        $query->addOrderBy($rootAlias.'.nom', 'ASC');
        
        return $query;
   }

   protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('full_text', CallbackFilter::class, [
                'callback' => [$this, 'getFullNameFilter'],
                'field_type' => TextType::class,
                'label' => 'Nom complet',
            ])
            ->add('datenaiss', DateTimeFilter::class, array('label' => 'Date de naissance'))
            ->add('account', null, [
                'field_type' => EntityType::class,
                'field_options' => [
                    'class' => User::class,
                    'choice_label' => 'username',
                ],
            ]);
    }

   public function getFullNameFilter(ProxyQueryInterface $query, string $alias, string $field, FilterData $data): bool
    {
        if (!$data->hasValue()) {
            return false;
        }

        $query->andWhere($query->expr()->orX(
            $query->expr()->like($alias.'.nom', $query->expr()->literal('%' . $data->getValue() . '%')),
            $query->expr()->like($alias.'.prenoms', $query->expr()->literal('%' . $data->getValue() . '%'))
        ));

        return true;
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
            $menu->addChild('Positions', $admin->generateMenuUrl('admin.personnel_position.list', ['id' => $id]));
            $menu->addChild('Contrats', $admin->generateMenuUrl('admin.contrat.list', ['id' => $id]));
            $menu->addChild('Diplome', $admin->generateMenuUrl('admin.diplome.list', ['id' => $id]));
            $menu->addChild('Certificat Aptitude ', $admin->generateMenuUrl('admin.aptitude.list', ['id' => $id]));
            $menu->addChild('Assurance', $admin->generateMenuUrl('admin.insurance.list', ['id' => $id]));
        }
    }
}
