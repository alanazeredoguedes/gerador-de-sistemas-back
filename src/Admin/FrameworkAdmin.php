<?php
namespace App\Admin;

use App\Entity\Framework;
use App\Entity\ProgrammingLanguage;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class FrameworkAdmin extends AbstractAdmin
{

    public function toString(object $object): string
    {
        return $object instanceof Framework ? $object->getId() : '';
    }

   /* protected function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'Framework';
    }*/

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        //$collection->add('login');
        //$collection->add('logout');
        //$collection->remove('edit');
    }

    protected function configureFormFields(FormMapper $form): void
    {

        $form->with('Informações Gerais', ['class' => 'col-md-8']);

            $form->add('name', TextType::class,  [
                'label' => 'Nome',
            ]);
            $form->add('description', TextType::class, [
                'label' => 'Descrição',
            ]);
            $form->add('active', CheckboxType::class, [
                'label' => 'Ativo',
                'required' => false
            ]);
            $form->add('programmingLanguage', ModelType::class,[
                'class' => ProgrammingLanguage::class,
                'property' => 'name',
                'label' => 'Linguagem de Programação',
                'required' => true,
                'expanded' => false,
                'btn_add' => false,
                'multiple' => false,
            ]);

        $form->end();

        $form->with('Imagem do Framework', ['class' => 'col-md-4']);

            $form->add('logo', ModelListType::class,[
                'label' => 'Logo Do Framework: ',

            ]/*,[
                'link_parameters' => [
                    'context' => 'default',
                    // 'hide_context' => true,
                ],
            ]*/);

        $form->end();

    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('name');
        $datagrid->add('description');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('logo', null, [
            'template' => '@SonataMedia/MediaAdmin/list_image.html.twig'
        ]);
        $list->addIdentifier('name', null, [
            'label' => 'nome',
            'route' => ['name' => 'edit']
        ]);
        $list->addIdentifier('description',null, [
            'label' => 'Descrição',
        ]);
        $list->addIdentifier('active',null , [
            'label' => 'Ativo',
        ]);
        $list->addIdentifier('programmingLanguage',null,[
            'associated_property' => 'name',
            'label' => 'Linguagem de Programação',
        ]);
        $list->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
            'actions' => [
                'show' => [],
                'edit' => [],
                'delete' => [],
            ]
        ]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('name');
        $show->add('description');
    }
}