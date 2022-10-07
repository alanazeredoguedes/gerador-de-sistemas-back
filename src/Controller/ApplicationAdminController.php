<?php

namespace App\Controller;

use App\Application\Project\ContentBundle\Attributes\ARR;
use App\Application\Project\ContentBundle\Controller\DefaultCRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[ARR(groupName: 'Aplicação', description: 'Permissões do modulo de Aplicação')]
class ApplicationAdminController extends DefaultCRUDController
{

    #[ARR(routerName: 'listAction', role: "ROLE_ADMIN_APPLICATION_LIST", title: 'Listar')]
    public function listAction(Request $request): Response
    {
        /** Access Control Validate */
        $this->validateAccess("ROLE_ADMIN_APPLICATION_LIST");

        return parent::listAction($request);
    }

    #[ARR(routerName: 'showAction', role: "ROLE_ADMIN_APPLICATION_SHOW", title: 'Visualizar')]
    public function showAction(Request $request): Response
    {
        /** Access Control Validate */
        $this->validateAccess("ROLE_ADMIN_APPLICATION_SHOW");

        return parent::showAction($request);
    }

    #[ARR(routerName: 'createAction', role: "ROLE_ADMIN_APPLICATION_CREATE", title: 'Criar')]
    public function createAction(Request $request): Response
    {
        /** Access Control Validate */
        $this->validateAccess("ROLE_ADMIN_APPLICATION_CREATE");

        return parent::createAction($request);
    }

    #[ARR(routerName: 'editAction', role: "ROLE_ADMIN_APPLICATION_EDIT", title: 'Editar')]
    public function editAction(Request $request): Response
    {
        /** Access Control Validate */
        $this->validateAccess("ROLE_ADMIN_APPLICATION_EDIT");

        return parent::editAction($request);
    }

    #[ARR(routerName: 'deleteAction', role: "ROLE_ADMIN_APPLICATION_DELETE", title: 'Excluir')]
    public function deleteAction(Request $request): Response
    {
        /** Access Control Validate */
        $this->validateAccess("ROLE_ADMIN_APPLICATION_DELETE");

        return parent::deleteAction($request);
    }

    #[ARR(routerName: 'batchAction', role: "ROLE_ADMIN_APPLICATION_BATCH", title: 'Excluir em Lote')]
    public function batchActionDelete(ProxyQueryInterface $query): Response
    {
        /** Access Control Validate */
        $this->validateAccess("ROLE_ADMIN_APPLICATION_BATCH");

        return parent::batchActionDelete($query);
    }

    public function batchAction(Request $request): Response
    {
        /** Access Control Validate */
        $this->validateAccess("ROLE_ADMIN_APPLICATION_BATCH");

        return parent::batchAction($request);
    }

    #[ARR(routerName: 'exportAction', role: "ROLE_ADMIN_APPLICATION_EXPORT", title: 'Exportar')]
    public function exportAction(Request $request): Response
    {
        /** Access Control Validate */
        $this->validateAccess("ROLE_ADMIN_APPLICATION_EXPORT");

        return parent::exportAction($request);
    }

    #[ARR(routerName: 'historyAction', role: "ROLE_ADMIN_APPLICATION_HISTORY", title: 'Auditoria')]
    public function historyAction(Request $request): Response
    {
        /** Access Control Validate */
        $this->validateAccess("ROLE_ADMIN_APPLICATION_HISTORY");

        return parent::historyAction($request);
    }

    public function historyViewRevisionAction(Request $request, string $revision): Response
    {
        /** Access Control Validate */
        $this->validateAccess("ROLE_ADMIN_APPLICATION_HISTORY");


        return parent::historyViewRevisionAction($request, $revision);
    }

    public function historyCompareRevisionsAction(Request $request, string $baseRevision, string $compareRevision): Response
    {
        /** Access Control Validate */
        $this->validateAccess("ROLE_ADMIN_APPLICATION_HISTORY");

        return parent::historyCompareRevisionsAction($request, $baseRevision, $compareRevision);
    }


}