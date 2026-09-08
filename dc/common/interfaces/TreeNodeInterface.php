<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 22.09.2017
 * Time: 21:38
 */

namespace DynCom\dc\common\interfaces;


interface TreeNodeInterface
{
    public function getLeftSibling() : ?TreeNodeInterface;
    public function getRightSibling() : ?TreeNodeInterface;
    public function getParent() : ?TreeNodeInterface;
    public function getChildren() : array;
    public function getLeftmostChild() : ?TreeNodeInterface;
    public function getRightmostChild() : ?TreeNodeInterface;
    public function getNoOfChildren(): int;
    public function getNoOfDescendants(): int;
    public function isLeaf(): bool;
    public function isRoot(): bool;
    public function getDepth(): int;
    public function getHeight(): int;
}