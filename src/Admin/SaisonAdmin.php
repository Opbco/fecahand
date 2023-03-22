<?php

namespace App\Admin;

use App\Entity\Saison;
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

final class SaisonAdmin extends AbstractAdmin
{
    private $container;

    public function __construct(ContainerInterface $containerInterface)
    {
        $this->container = $containerInterface;
    }

    public function toString(object $object): string
    {
        return $object instanceof Saison
            ? 'Saison ' . $object->getNom()
            : 'Saison'; // shown in the breadcrumb on the create view
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('nom')
            ->assertNotBlank()
            ->assertNotNull()
            ->end();
    }

	  protected function prePersist(object $object): void
    {
        $user = $this->ts->getToken()->getUser();
        $object->setDateCreated(new \DateTimeImmutable());
        $object->setUserCreated($user);
    }
	
    protected function configureFormFields(FormMapper $form): void
    {
        $form->tab('Saison')
			->with("Details", ['class' => 'col-md-6'])
				->add('nom', TextType::class, array('label' => 'Nom de la saison', 'required' => true))
				->add('dateDebut', DatePickerType::class, [
	                'label' => 'Date de debut de saison',
	                'required' => true
	            ])
				->add('DateFin', DatePickerType::class, [
	                'label' => 'Date de fin de saison',
	                'required' => true
	            ])
				->add('montantAffiliation', FloatType::class, array('label' => "Montant de l'affiliation (Fcfa)", 'required' => true))
				->add('montantLicenceJoueur', FloatType::class, array('label' => 'Montant de la licence du joueur (Fcfa)', 'required' => true))
				->add('montantLicenceArbitre', FloatType::class, array('label' => "Montant de la licence de l'arbitre (Fcfa)", 'required' => true))				
				->add('licences', CollectionType::class, [
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
            ->add('nom', null, ['label'=>'Nom de la saison'])			
			->add('dateDebut', null, ['label' => 'Date de debut de saison'])
			->add('DateFin', null, ['label' => 'Date de fin de saison'])
			->add('montantAffiliation', null, ['label' => "Montant de l'affiliation (Fcfa)"])
			->add('montantLicenceJoueur', null, ['label' => 'Montant de la licence du joueur (Fcfa)'])
			->add('montantLicenceArbitre', null, ['label' => "Montant de la licence de l'arbitre (Fcfa)"]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
		$form->tab('Saison')
			->with("Details", ['class' => 'col-md-6'])
				->add('nom', null, ['label' => 'Nom de la saison'])
				->add('dateDebut', null, ['label' => 'Date de debut de saison'])
				->add('DateFin', null, ['label' => 'Date de fin de saison'])
				->add('montantAffiliation', null, ['label' => "Montant de l'affiliation (Fcfa)"])
				->add('montantLicenceJoueur', null, ['label' => 'Montant de la licence du joueur (Fcfa)'])
				->add('montantLicenceArbitre', null, ['label' => "Montant de la licence de l'arbitre (Fcfa)"])			
			->end()
		->end();
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        // display the first page (default = 1)
        $sortValues[DatagridInterface::PAGE] = 1;

        // reverse order (default = 'DESC')
        $sortValues[DatagridInterface::SORT_ORDER] = 'ASC';

        // name of the ordered field (default = the model's id field, if any)
        $sortValues[DatagridInterface::SORT_BY] = 'nom';
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
     	$datagrid->add('nom', null, array('label' => 'Nom de la discipline affinitaire'))
			->add('dateDebut', DateTimeFilter::class, ['label' => 'Date de debut de saison'])
			->add('DateFin', DateTimeFilter::class, ['label' => 'Date de fin de saison'])
			->add('montantAffiliation', null, ['label' => "Montant de l'affiliation (Fcfa)"])
			->add('montantLicenceJoueur', null, ['label' => 'Montant de la licence du joueur (Fcfa)'])
			->add('montantLicenceArbitre', null, ['label' => "Montant de la licence de l'arbitre (Fcfa)"]);
    }
    
}