<?php
namespace DynCom\dc\common\classes;
use DynCom\dc\common\interfaces\PHTMLTemplate;
use DynCom\dc\common\interfaces\Repository;
use DynCom\dc\common\interfaces\RepositoryListView;
use DynCom\dc\common\traits\genericRepositoryListViewTrait;

/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 25.07.2015
 * Time: 15:21
 */
class GenericPHTMLRepositoryListView implements RepositoryListView
{
    use genericRepositoryListViewTrait;

    protected $template;
    protected $model;
    protected $renderedContent = false;
    protected $repository;

    /**
     * GenericPHTMLRepositoryListView constructor.
     * @param PHTMLTemplate $listElementTemplate
     * @param Repository $repository
     */
    public function __construct(PHTMLTemplate $listElementTemplate, Repository $repository) {
        $this->template = $listElementTemplate;
        $this->repository = $repository;
    }

    /**
     * @param array $criteria
     * @param int $offset
     * @param int $limit
     * @return bool|string
     */
    public function render(array $criteria, $offset = 0, $limit = 0) {
        if($this->renderedContent) return $this->renderedContent;
        $renderedString = '';
        $collection = $this->repository->findByCriteria($criteria,$offset,$limit);
        foreach($collection as $listElement) {
            $this->model = $listElement;
            ob_start();
            include_once($this->template->getPHTMLPath());
            $renderedString .= ob_get_clean();
        }
        $this->renderedContent = $renderedString;
        return $this->renderedContent;
    }

}