<?php

namespace App\Admin;

use App\Entity\Regle;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\Form\Validator\ErrorElement;
use Sonata\Form\Type\DatePickerType;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class RegleAdmin extends AbstractAdmin
{
    private $container;

    public function __construct(ContainerInterface $containerInterface)
    {
        $this->container = $containerInterface;
    }

    public function toString(object $object): string
    {
        return $object instanceof Regle
            ? 'Regle ' . $object->getNom()
            : 'Regle'; // shown in the breadcrumb on the create view
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
		$regle = $this->getSubject();

        // use $fileFormOptions so we can add other options to the field
        $fileFormFOptions = ['required' => false];

        if ($regle && ($webPath = $regle->getPdfWebPath())) {
            // get the request so the full path to the image can be set
            $fullPath = $regle->getPdfAbsolutePath();
            // add a 'help' option containing the preview's img tag
            $fileFormFOptions['help'] = is_file($fullPath) ? '<a href="' . $webPath . '">Click to download</a>' : 'copie mumerique non disponible';
            $fileFormFOptions['help_html'] = true;
        }

       $form->tab('Regle')
			->with("Details", ['class' => 'col-md-6'])
				->add('nom', TextType::class, array('label' => 'Nom de la regle', 'required' => true))
				->add('description', TextType::class, array('label' => 'Description de la regle', 'required' => true))
				->add('datePromulgation', DatePickerType::class, [
					'label' => 'Date de promulgation',
					'required' => true
				])
				->add('active', BooleanType::class, array('label' => 'Actif', 'required' => false))
				->add('pdfFile', FileType::class, $fileFormFOptions)			
			->end()			
		->end();
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('id', null, ['label'=>'ID'])
           	->add('nom', null, array('label' => 'Nom de la regle'))
			->add('description', null, array('label' => 'Description de la regle'))
			->add('datePromulgation', null,	array('label' => 'Date de promulgation'))
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
		$show->tab('Regle')
			->with("Details de regle", ['class' => 'col-md-6'])
				->add('nom', null, array('label' => 'Nom de la regle'))
				->add('description', null, array('label' => 'Description de la regle'))
				->add('datePromulgation', null,	array('label' => 'Date de promulgation'))
				->add('active', null, array('label' => 'Actif'))
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
				->add('datePromulgation', null,	array('label' => 'Date de promulgation'));
    }

}