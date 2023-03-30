<?php

namespace App\Admin;

use App\Entity\Bureau;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\CollectionType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\DateTimeFilter;
use Sonata\Form\Validator\ErrorElement;
use Sonata\Form\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class BureauAdmin extends AbstractAdmin
{

    public function toString(object $object): string
    {
        return $object instanceof Bureau
            ? $object->getNom()
            : 'Bureau'; // shown in the breadcrumb on the create view
    }

    protected function prePersist(object $object): void
    {
        $object->setActif(1);
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('nom')
                ->assertNotBlank()
            ->end()
            ->with('dateElection')
                ->assertNotBlank()
            ->end();
    }

    protected function configureFormFields(FormMapper $form): void
    {

        $form->tab("Bureau")
            ->with("Details", ['class' => 'col-md-8'])
                ->add('nom', TextType::class, array('label' => 'Nom', 'required' => true))
                ->add('dateElection', DatePickerType::class, array('label' => 'Date des elections', 'required' => true))
            ->end()
        ->end()
        ->tab("Positions")
            ->with('Les Positions posibles', ['class' => 'col-md-12'])
                ->add('positions', CollectionType::class, [
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
        $list->addIdentifier('id', null, ['label'=>'ID'])
            ->add('nom', null, ['label'=>'Nom'])
            ->add('dateElection', null, ['label'=>'Date des elections'])
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

        $show->tab("Bureau")
            ->with("Details", ['class' => 'col-md-8'])
                ->add('dateElection', null, array('label' => 'Date des elections'))
                ->add('nom', null, array('label' => 'Nom'))
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
        $sortValues[DatagridInterface::SORT_BY] = 'dateElection';
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('nom', null, array('label' => 'Nom'))
            ->add('dateElection', DateTimeFilter::class, array('label' => 'Date dernieres elections'));
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
            $menu->addChild('Personnes', $admin->generateMenuUrl('admin.bureau_personnes.list', ['id' => $id]));
        }
    }

}
