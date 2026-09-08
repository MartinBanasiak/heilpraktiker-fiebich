<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 25.07.2015
 * Time: 11:07
 */
interface PHTMLRepositoryListView extends RepositoryListView
{

    /**
     * PHTMLRepositoryListView constructor.
     * @param PHTMLTemplate $listElementTemplate
     * @param Repository $repository
     */
    public function __construct(PHTMLTemplate $listElementTemplate, Repository $repository);

}