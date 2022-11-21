<?php
namespace App\Admin;

use App\Application\Project\ContentBundle\Service\RolesIdentifierService;
use App\Application\Project\UserBundle\Entity\User;
use App\Entity\Diagram;
use Knp\Menu\ItemInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class DiagramAdmin extends AbstractAdmin
{

    public function toString(object $object): string
    {
        return $object instanceof Diagram ? $object->getId() : '';
    }

   /* protected function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'Diagram';
    }*/

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        //$collection->add('login');
        //$collection->add('logout');
        //$collection->remove('edit');
    }

    protected function configureFormFields(FormMapper $form): void
    {

        $form->add('name', TextType::class);
        $form->add('description', TextType::class);
        $form->add('structure', TextareaType::class);
        $form->add('user', ModelType::class,[
            'class' => User::class,
            'property' => 'username',
            'label' => 'Usuario',
            'required' => true,
            'expanded' => false,
            'btn_add' => false,
            'multiple' => false,
        ]);
        $form->add('isTemplate', CheckboxType::class, [
            'label' => 'É um diagrama template ?',
            'required' => false,
        ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('name');
        $datagrid->add('description');
        $datagrid->add('structure');
        $datagrid->add('isTemplate',null,[
            'label' => 'Diagrama template',
        ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('name', null, [
            'route' => ['name' => 'edit'],
            'label' => 'Nome',
        ]);
        $list->addIdentifier('description', null, [
            'label' => 'Descrição',
        ]);
        $list->add('user',null,[
            'associated_property' => 'username',
            'label' => 'Usuario',
        ]);
        $list->add('isTemplate',null,[
            'label' => 'É um diagrama template?',
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
        $show->add('structure');
        $show->add('isTemplate',null,[
            'label' => 'É Um diagrama template?',
        ]);
    }
}