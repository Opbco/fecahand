<?php

namespace App\Admin;

use APP\Entity\DisciplineAffinitaire;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateTimeFilter;
use Sonata\Form\Type\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\Form\Validator\ErrorElement;
use Sonata\Form\Type\DatePickerType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class DisciplineAffinitaireAdmin extends AbstractAdmin
{
	private $container;

	public function __construct(ContainerInterface $containerInterface)
	{
		$this->container = $containerInterface;
	}

	public function toString(object $object): string
	{
		return $object instanceof DisciplineAffinitaire
			? 'Discipline Affinitaire ' . $object->getNom()
			: 'Discipline Affinitaire'; // shown in the breadcrumb on the create view
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
		$form->tab('DisciplineAffinitaire')
			->with("Details", ['class' => 'col-md-6'])
			->add('dateCreated', DatePickerType::class, array('label' => 'Date de creation', 'required' => true))
			->add('nom', TextType::class, array('label' => 'Nom de la discipline affinitaire', 'required' => true))
			->add('description', TextareaType::class, array('label' => 'Description de la discipline', 'required' => true))
			->add('dureeMandatSec', NumberType::class, array('label' => 'Duree du mandat (secretariat general)', 'required' => true))
			->add('active', BooleanType::class, array('label' => 'Actif', 'required' => false))
			->end()
			->with("Details joueurs", ['class' => 'col-md-6'])
			->add('nombreJoueur', NumberType::class, array('label' => 'Nombre de joueur par equipe', 'required' => true))
			->add('nombreJoueurStade', NumberType::class, array('label' => 'Nombre de joueur au stade par equipe', 'required' => true))
			->end()
			->with("Details balle", ['class' => 'col-md-6'])
			->add('typeBalle', ChoiceType::class, [
				'choices' => [
					'Rond' => 'Rond',
					'Oval' => 'Oval',
					'Plat' => 'Plat',
				],
				'label' => 'Choisi le type de balle',
				'required' => true
			])
			->add('dimensionBalle', TextType::class, ['label' => 'Volume de la balle (m3)'])
			->end()
			->end();
	}

	protected function configureListFields(ListMapper $list): void
	{
		$list->addIdentifier('id', null, ['label' => 'ID'])
			->add('nom', null, ['label' => 'Nom de la discipline affinitaire'])
			->add('nombreJoueur', null, ['label' => 'Nombre de total joueurs'])
			->add('typeBalle', null, ['label' => 'Type de balle'])
			->add('description', null, ['label' => 'Description'])
			->add('active', null, [
				'editable' => true, 'label' => 'Actif'
			])
			->add(ListMapper::NAME_ACTIONS, null, [
				'actions' => [
					'delete' => [],
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
		$show->tab('DisciplineAffinitaire')
			->with("Details discipline affinitaire", ['class' => 'col-md-6'])
			->add('dateCreated', null, array('label' => 'Date de creation'))
			->add('nom', null, ['label' => 'Nom de la discipline affinitaire'])
			->add('nombreJoueur', null, ['label' => 'Nombre de joueurs'])
			->add('nombreJoueurStade', null, ['label' => 'Nombre de joueurs au stade'])
			->add('typeBalle', null, ['label' => 'Type de balle'])
			->add('dimensionBalle', null, ['label' => 'Volume de la balle (m3)'])
			->add('description', null, ['label' => 'Description'])
			->add('active', null, array('label' => 'Actif'))
			->add('dureeMandatSec', null, ['label' => 'Duree du mandat en seconde'])
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
			->add('nombreJoueur', null, ['label' => 'Nombre de joueurs'])
			->add('nombreJoueurStade', null, ['label' => 'Nombre de joueurs au stade'])
			->add('dateCreated', DateTimeFilter::class, array('label' => 'Date de creation'))
			->add('typeBalle', ChoiceFilter::class, [
				'label' => 'State',
				'field_type' => ChoiceType::class,
				'field_options' => [
					'choices' => [
						'Rond' => 'Rond',
						'Oval' => 'Oval',
						'Plat' => 'Plat',
					],
				],
			]);
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
			$menu->addChild('Regles', $admin->generateMenuUrl('admin.discipline_regles.list', ['id' => $id]));
		}
	}
}
