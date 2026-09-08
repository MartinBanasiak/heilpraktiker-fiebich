<?php
/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 22.09.2017
 * Time: 21:34
 */

namespace DynCom\dc\dcShop\classes;


use DynCom\dc\common\interfaces\TreeNodeInterface;

class TreeNodeVisitor
{

    /**
     * @var callable
     */
    protected $visit;

    public function __construct(callable $visitingFunction)
    {
        $this->visit = $visitingFunction;
    }

    public function visitPreorder(?TreeNodeInterface $node): void
    {
        if (null === $node) {
            return;
        }
        ($this->visit)($node);
        $this->visitPreorder($node->getLeftmostChild());
        $this->visitPreorder($node->getRightSibling());
    }

    public function visitPostorder(?TreeNodeInterface $node): void
    {
        if (null === $node) {
            return;
        }
        $this->visitPostorder($node->getLeftmostChild());
        ($this->visit)($node);
        $this->visitPostorder($node->getRightSibling());
    }


    public function visitByLevel(?TreeNodeInterface $node): void
    {
        $queue = [];
        $queue[] = $node;
        while (!empty($queue)) {
            /**
             * @var $currNode TreeNodeInterface
             */
            $currNode = array_shift($queue);
            ($this->visit)($currNode);
            for ($iterationNode = $currNode->getLeftmostChild();$iterationNode !== null;$iterationNode = $iterationNode->getRightSibling())
            {
                $queue[] = $iterationNode;
            }
        }
    }

}