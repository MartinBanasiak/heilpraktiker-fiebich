<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 14.09.2017
 * Time: 11:12
 */

namespace DynCom\dc\dcShop\classes;


use DynCom\dc\common\interfaces\TreeNodeInterface;
use DynCom\dc\dcShop\interfaces\CategoryTreeNodeInterface;

class CategoryTreeNode implements CategoryTreeNodeInterface
{

    /**
     * @var int
     */
    protected $id;
    /**
     * @var string
     */
    protected $company;
    /**
     * @var string
     */
    protected $shopCode;
    /**
     * @var string
     */
    protected $languageCode;
    /**
     * @var int
     */
    protected $lineNo;
    /**
     * @var string
     */
    protected $code;
    /**
     * @var ?CategoryTreeNodeInterface
     */
    protected $left;

    /**
     * @var ?CategoryTreeNodeInterface
     */
    protected $right;
    /**
     * @var ?CategoryTreeNodeInterface
     */
    protected $parent;
    /**
     * @var array
     */
    protected $children = [];
    /**
     * @var int
     */
    protected $depth = 0;
    /**
     * @var int
     */
    protected $height = 0;

    /**
     * @var Category
     */
    protected $category;

    public function __construct(int $id, string $company, string $shopCode, string $languageCode, int $lineNo, string $code, Category $category)
    {
        $this->id = $id;
        $this->company = $company;
        $this->shopCode = $shopCode;
        $this->languageCode = $languageCode;
        $this->lineNo = $lineNo;
        $this->code = $code;
        $this->category = $category;
        $this->code = $code;
    }


    public function setLeftSibling(CategoryTreeNodeInterface $leftSibling):void
    {
        $this->left = $leftSibling;
        if ($leftSibling->getRightSibling() !== $this) {
            $leftSibling->setRightSibling($this);
        }

    }

    public function setRightSibling(CategoryTreeNodeInterface $rightSibling):void
    {
        $this->right = $rightSibling;
        if ($rightSibling->getLeftSibling() !== $this) {
            $rightSibling->setLeftSibling($this);
        }
    }

    public function setParent(CategoryTreeNodeInterface $parent) : void
    {
        $this->parent = $parent;
        if (!$parent->hasChild($this)) {
            $parent->addChild($this);
        }
    }

    public function addChild(CategoryTreeNodeInterface $child) : void
    {
        $this->children[$child->getLineNo()] = $child;
        if ($child->getParent() !== $this) {
            $child->setParent($this);
        }
    }

    public function hasChild(CategoryTreeNodeInterface $child) : bool
    {
        return array_key_exists($child->getLineNo(),$this->children);
    }

    public function getID(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function getShopCode(): string
    {
        return $this->shopCode;
    }

    /**
     * @return string
     */
    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    /**
     * @return int
     */
    public function getLineNo(): int
    {
        return $this->lineNo;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    public function getLeftSibling(): ?TreeNodeInterface
    {
        return $this->left;
    }

    public function getRightSibling(): ?TreeNodeInterface
    {
        return $this->right;
    }

    public function getParent(): ?TreeNodeInterface
    {
        return $this->parent;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getChildByLineNo(int $lineNo) : ?CategoryTreeNodeInterface
    {
        if (array_key_exists($lineNo,$this->children)) {
            return $this->children[$lineNo];
        }
        return null;
    }

    public function getLeftmostChild() : ?TreeNodeInterface
    {
        if ($this->getNoOfChildren() > 0) {
            $vals = array_values($this->children);
            return $vals[0];
        }
        return null;
    }

    public function getRightmostChild() : ?TreeNodeInterface
    {
        if ($this->getNoOfChildren() > 0) {
            $vals = array_values($this->children);
            return end($vals);
        }
        return null;
    }

    public function isLeaf(): bool
    {
        return empty($this->children);
    }

    public function isRoot(): bool
    {
        return null === $this->parent;
    }

    public function getDepth(): int
    {
        $depth = 0;
        if ($this->isRoot()) {
            return $depth;
        }
        $parent = $this->getParent();
        while (null !== $parent) {
            $depth++;
            $parent = $parent->getParent();
        }
        return $depth;
    }

    public function getHeight(): int
    {
        $height = 0;
        if ($this->isLeaf()) {
            return $height;
        }
        $maxChildHeight = 0;
        /**
         * @var $child CategoryTreeNodeInterface
         */
        foreach ($this->children as $child) {
            $childHeight = $child->getHeight();
            if ($childHeight > $maxChildHeight) {
                $maxChildHeight = $childHeight;
            }
        }
        $height = $maxChildHeight + 1;
        return $height;
    }

    public function getNoOfChildren(): int
    {
        return count($this->children);
    }

    public function getNoOfDescendants(): int
    {
        $noOfDescendants = 0;
        $noOfDescendants += count($this->children);

        /**
         * @var $child CategoryTreeNodeInterface
         */
        foreach ($this->children as $child) {
            $noOfDescendants += $child->getNoOfDescendants();
        }
        return $noOfDescendants;
    }

    public function visitByBreadthLeftToRight(callable $function)
    {
        $function($this);

        $currNode = reset($this->children);
        $nextLevelIterationSet = [];


        while ($currNode instanceof CategoryTreeNodeInterface) {
            $function($currNode);
            $currRightSibling = $currNode->getRightSibling();
            array_merge($nextLevelIterationSet,$currNode->getChildren());
            if ($currRightSibling instanceof CategoryTreeNodeInterface) {
                $currNode = $currRightSibling;
            } else {
                $currNode = array_shift($nextLevelIterationSet);
            }
        }
    }

    public function visitByBreadthRightToLeft(callable $function)
    {
        $function($this);

        $currNode = end($this->children);
        $nextLevelIterationSet = [];


        while ($currNode instanceof CategoryTreeNodeInterface) {
            $function($currNode);
            $currLeftSibling = $currNode->getLeftSibling();
            array_merge($nextLevelIterationSet,$currNode->getChildren());
            if ($currLeftSibling instanceof CategoryTreeNodeInterface) {
                $currNode = $currLeftSibling;
            } else {
                $currNode = array_pop($nextLevelIterationSet);
            }
        }
    }

    public function visitByDepthPreorder(callable $function)
    {
        $function($this);

        $currNode = reset($this->children);
        $currParent = $this;
        $currParentChildrenByParentLineNo = [];
        $currParentChildrenByParentLineNo[$this->getLineNo()] = $this->children;

        while ($currNode instanceof CategoryTreeNodeInterface) {
            $function($currNode);
            $currLeftmostChild = $currNode->getLeftmostChild();
            $currRightSibling = $currNode->getRightSibling();
            if ($currLeftmostChild instanceof CategoryTreeNodeInterface) {
                $currParent = $currNode->getParent();
                $currParentChildrenByParentLineNo[$currParent->getLineNo()] = $currParent->getChildren();
                $currNode = $currLeftmostChild;
            } elseif($currRightSibling instanceof CategoryTreeNodeInterface) {
                $currNode = $currRightSibling;
            } else {
                $currNode = next($currParentChildrenByParentLineNo[$currParent->getLineNo()]);
            }
        }
    }

    public function visitByDepthPostorder(callable $function)
    {

    }


    public function getCategory() : Category
    {
        return $this->category;
    }
}