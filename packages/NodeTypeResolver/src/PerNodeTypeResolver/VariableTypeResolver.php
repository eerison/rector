<?php declare(strict_types=1);

namespace Rector\NodeTypeResolver\PerNodeTypeResolver;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Variable;
use Rector\Node\Attribute;
use Rector\NodeTypeResolver\Contract\NodeTypeResolverAwareInterface;
use Rector\NodeTypeResolver\Contract\PerNodeTypeResolver\PerNodeTypeResolverInterface;
use Rector\NodeTypeResolver\NodeTypeResolver;
use Rector\NodeTypeResolver\TypeContext;

final class VariableTypeResolver implements PerNodeTypeResolverInterface, NodeTypeResolverAwareInterface
{
    /**
     * @var TypeContext
     */
    private $typeContext;

    /**
     * @var NodeTypeResolver
     */
    private $nodeTypeResolver;

    public function __construct(TypeContext $typeContext)
    {
        $this->typeContext = $typeContext;
    }

    public function getNodeClass(): string
    {
        return Variable::class;
    }

    /**
     * @param Variable $variableNode
     */
    public function resolve(Node $variableNode): ?string
    {
        $parentNode = $variableNode->getAttribute(Attribute::PARENT_NODE);
        if ($parentNode instanceof Assign) {
            return $this->processVariableTypeForAssign($variableNode, $parentNode);
        }

        if ($variableNode->name instanceof Variable) {
            // nested: ${$type}[$name] - dynamic, unable to resolve type
            return null;
        }

        $variableType = $this->typeContext->getTypeForVariable((string) $variableNode->name);
        if ($variableType) {
            return $variableType;
        }

        if ($variableNode->name === 'this') {
            return $variableNode->getAttribute(Attribute::CLASS_NAME);
        }

        return null;
    }

    public function setNodeTypeResolver(NodeTypeResolver $nodeTypeResolver): void
    {
        $this->nodeTypeResolver = $nodeTypeResolver;
    }

    private function processVariableTypeForAssign(Variable $variableNode, Assign $assignNode): ?string
    {
//        $variableType = $this->processAssignVariableNode($assignNode);

        if ($assignNode->expr instanceof New_) {
            $variableName = $variableNode->name;
            $variableType = $this->nodeTypeResolver->resolve($assignNode->expr);

            $this->typeContext->addVariableWithType($variableName, $variableType);

            return $variableType;
        }

        if ($variableNode->name instanceof Variable) {
            $name = $variableNode->name->name;

            return $this->typeContext->getTypeForVariable($name);
        }

        $name = (string) $variableNode->name;

        return $this->typeContext->getTypeForVariable($name);
    }

    private function processAssignVariableNode(Assign $assignNode): ?string
    {
        if ($assignNode->var->name instanceof Variable) {
            $name = $assignNode->var->name->name;
        } else {
            $name = $assignNode->var->name;
        }

        $this->typeContext->addAssign($name, $assignNode->expr->name);

        $variableType = $this->typeContext->getTypeForVariable($name);
        if ($variableType) {
            $assignNode->var->setAttribute(Attribute::TYPE, $variableType);

            return $variableType;
        }

        return null;
    }
}
