<?php

namespace App\Admin;

use App\Entity\Diplome;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class PositionAdmin extends AbstractAdmin
{

    public function toString(object $object): string
    {
        return $object->getNom(); // shown in the breadcrumb on the create view
    }

    protected function prePersist(object $object): void
    {

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

        $form->tab('Position')
                ->with("Details", ['class' => 'col-md-8'])
                    ->add('nom', TextType::class, array('label' => 'Nom du diplome', 'required' => true))
                    ->add('responsable', ChoiceType::class, array('choices'=> [
                        'Choisir' => '',
                        'oui' => true,
                        'non' => false,
                    ],'label' => 'Est-il responsable?', 'required' => true))
                    ->add('licenced', ChoiceType::class, array('choices'=> [
                        'Choisir' => '',
                        'oui' => true,
                        'non' => false,
                    ],'label' => 'Paie t-il une licence? ', 'required' => true))
                    ->add('insured', ChoiceType::class, array('choices'=> [
                        'Choisir' => '',
                        'oui' => true,
                        'non' => false,
                    ],'label' => 'Doit-il etre assure?', 'required' => true))
                    ->add('apte', ChoiceType::class, array('choices'=> [
                        'Choisir' => '',
                        'oui' => true,
                        'non' => false,
                    ],'label' => 'Doit-il etre apte?', 'required' => true))
                ->end()
            ->end();
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('id', null, ['label'=>'ID'])
            ->add('nom', null, ['label'=>'Nom'])
            ->add('responsable', null, [
                'editable' => true, 'label' => 'Responsable'
            ])
            ->add('licenced', null, [
                'editable' => true, 'label' => 'Paie une licence'
            ])
            ->add('insured', null, [
                'editable' => true, 'label' => 'Est assure'
            ])
            ->add('apte', null, [
                'editable' => true, 'label' => 'Est apte'
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

        $show->tab('Position')
            ->with("Details", ['class' => 'col-md-8'])
                ->add('nom', null, array('label' => 'Nom'))
                ->add('responsable', null, array('label' => 'Est-il responsable?'))
                ->add('licenced', null, array('label' => 'Paie t-il une licence? '))
                ->add('insured', null, array('label' => 'Doit-il etre assure?'))
                ->add('apte', null, array('label' => 'Doit-il etre apte?'))
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
        $sortValues[DatagridInterface::SORT_BY] = 'nom';
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('nom', null, array('label' => 'Nom'))
            ->add('responsable', null, array('label' => 'Est-il responsable?'))
            ->add('licenced', null, array('label' => 'Paie t-il une licence? '))
            ->add('insured', null, array('label' => 'Doit-il etre assure?'))
            ->add('apte', null, array('label' => 'Doit-il etre apte?'));
    }

}
