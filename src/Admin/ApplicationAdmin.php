<?php
namespace App\Admin;

use App\Application\Project\ContentBundle\Service\RolesIdentifierService;
use App\Application\Project\UserBundle\Entity\User;
use App\Entity\Application;
use App\Entity\Diagram;
use App\Entity\Framework;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class ApplicationAdmin extends AbstractAdmin
{

    public function toString(object $object): string
    {
        return $object instanceof Application ? $object->getId() : '';
    }

   /* protected function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'Application';
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
        $form->add('user', ModelType::class,[
            'class' => User::class,
            'property' => 'username',
            'label' => 'Usuário',
            'required' => true,
            'expanded' => false,
            'btn_add' => false,
            'multiple' => false,
        ]);
        $form->add('diagram', ModelType::class,[
            'class' => Diagram::class,
            'property' => 'name',
            'label' => 'Diagrama',
            'required' => true,
            'expanded' => false,
            'btn_add' => false,
            'multiple' => false,
        ]);
        $form->add('framework', ModelType::class,[
            'class' => Framework::class,
            'property' => 'name',
            'label' => 'Framework',
            'required' => true,
            'expanded' => false,
            'btn_add' => false,
            'multiple' => false,
        ]);
        $form->add('lastGenerate', DateTimeType::class, [
            'label' => 'Ultima Vez Gerado',
            'required' => false,
        ]);
        $form->add('url', TextType::class, [
            'label' => 'Url Gerada',
            'required' => false,
        ]);
        $form->add('repository', TextType::class, [
            'label' => 'Repositorio Gerado',
            'required' => false,
        ]);

        $form->add('accessEmail', TextType::class, [
            'label' => 'Email para acesso',
            'required' => false,
        ]);
        $form->add('accessPassword', PasswordType::class, [
            'label' => 'Senha para acesso',
            'required' => false,
        ]);

    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('name');
        $datagrid->add('description');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('name', null, [
            'label' => 'Nome Aplicação',
            'route' => ['name' => 'edit']
        ]);
        /*$list->addIdentifier('description',null,[
            'label' => 'Descrição',
        ]);*/
        $list->addIdentifier('user',null,[
            'associated_property' => 'username',
            'label' => 'Usuário',
        ]);

        $list->add('diagram',null,[
            'associated_property' => 'name',
            'label' => 'Diagrama',
        ]);

        $list->add('framework',null,[
            'associated_property' => 'name',
            'label' => 'Framework',
        ]);

        $list->add('framework.programmingLanguage',null,[
            'associated_property' => 'name',
            'label' => 'Linguagem de Programação',
        ]);

        $list->add('lastGenerate', null, [
            'label' => 'Ultima vez gerado em:',
            'route' => ['name' => 'edit']
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
        $show->add('user',null,[
            'associated_property' => 'username',
            'label' => 'Usuário',
        ]);
        $show->add('lastGenerate');
        $show->add('url');
        $show->add('repository');

    }
}