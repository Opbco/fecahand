<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\League;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\StringListFilter;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class LeagueAdmin extends AbstractAdmin
{
    private $ts;

    public function __construct(TokenStorageInterface $ts)
    {
        $this->ts = $ts;
    }

    public function toString(object $object): string
    {
        return $object instanceof League
            ? $object->getNom()
            : 'League'; // shown in the breadcrumb on the create view
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
            ->with('nom')
                ->assertNotBlank()
                ->assertNotNull()
            ->end()
            ->with('userCreated')
                ->assertNotNull()
            ->end()
            ->with('typeLeague')
                ->assertNotNull()
                ->assertNotBlank()
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('nom')
            ->add('typeLeague', StringListFilter::class, [
                'field_type' => ChoiceType::class,
                'field_options' => [
                    'choices' => League::TYPE_LEAGUE_ASS,
                    'multiple' => true,
                ],
            ])
            ->add('dateCreation', null, ['label'=>'Date de creation'])
            ->add('departement', null, ['label'=>"Departement d'appartenance"])
            ->add('departement.region', null, ['label'=>"Region d'appartenance"])
            ->add('active')
            ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('nom')
            ->add('typeLeague', null, ['label'=>'Type de ligue'])
            ->add('dateCreation', null, ['label'=>'Date de creation'])
            ->add('departement', null, ['label'=>"Departement d'appartenance"])
            ->add('departement.region', null, ['label'=>"Region"])
            ->add('active', null, ['label'=>'Active', 'editable'=>true])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('nom')
            ->add('typeLeague', ChoiceType::class, array('choices'=> League::TYPE_LEAGUE_ASS, 'label' => 'Type de ligue', 'required' => true))
            ->add('dateCreation', DateType::class, ['label'=>'Date de creation'])
            ->add('departement', ModelAutocompleteType::class, [
                'label'        => "Departement d'appartenance",
                'required'     => true,
                'property'     => 'nom',
                'by_reference' => false,
            ])
            ->add('active')
            ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('nom')
            ->add('typeLeague', null, ['label'=>'Type de ligue'])
            ->add('dateCreation', null, ['label'=>'Date de creation'])
            ->add('departement', null, ['label'=>"Departement d'appartenance"])
            ->add('departement.region', null, ['label'=>"Region d'appartenance"])
            ->add('active')
            ;
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
            $menu->addChild('Clubs', $admin->generateMenuUrl('admin.club.list', ['id' => $id]));
        }
    }
}
