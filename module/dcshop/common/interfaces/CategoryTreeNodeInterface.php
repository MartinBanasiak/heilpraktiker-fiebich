<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 14.09.2017
 * Time: 11:15
 */

namespace DynCom\dc\dcShop\interfaces;


use DynCom\dc\common\interfaces\TreeNodeInterface;
use DynCom\dc\dcShop\classes\Category;

interface CategoryTreeNodeInterface extends TreeNodeInterface
{
    public function getID(): int;
    public function getCompany(): string;
    public function getShopCode(): string;
    public function getLanguageCode(): string;
    public function getLineNo(): int;
    public function getCode(): string;
    public function getCategory(): Category;
    public function setLeftSibling(CategoryTreeNodeInterface $leftSibling) : void;
    public function setRightSibling(CategoryTreeNodeInterface $rightSibling) : void;
    public function setParent(CategoryTreeNodeInterface $parent);
    public function addChild(CategoryTreeNodeInterface $child) : void;
    public function hasChild(CategoryTreeNodeInterface $child) : bool;
}