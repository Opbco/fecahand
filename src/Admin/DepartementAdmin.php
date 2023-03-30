<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Admin;

use App\Entity\Departement;
use App\Entity\Region;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DepartementAdmin extends AbstractAdmin {

    public function toString(object $object): string
    {
        return $object instanceof Departement
            ? $object->getNom()
            : 'Departement'; // shown in the breadcrumb on the create view
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form->add('nom', TextType::class, array('label' => 'Nom'))
                ->add('region', ModelListType::class, [
                    'class' => Region::class,
                    'label' => "Region",
                    'btn_delete' => false,
                ]);
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
                ->add('region');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('nom')
            ->add('region');
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