<?php

declare (strict_types=1);
namespace Rector\CodingStyle\ClassNameImport\ClassNameImportSkipVoter;

use RectorPrefix202311\Nette\Utils\Strings;
use PhpParser\Node;
use Rector\CodingStyle\ClassNameImport\ShortNameResolver;
use Rector\CodingStyle\Contract\ClassNameImport\ClassNameImportSkipVoterInterface;
use Rector\Core\Configuration\RenamedClassesDataCollector;
use Rector\Core\ValueObject\Application\File;
use Rector\StaticTypeMapper\ValueObject\Type\FullyQualifiedObjectType;
/**
 * Prevents adding:
 *
 * use App\SomeClass;
 *
 * If there is already:
 *
 * SomeClass::callThis();
 */
final class FullyQualifiedNameClassNameImportSkipVoter implements ClassNameImportSkipVoterInterface
{
    /**
     * @readonly
     * @var \Rector\CodingStyle\ClassNameImport\ShortNameResolver
     */
    private $shortNameResolver;
    /**
     * @readonly
     * @var \Rector\Core\Configuration\RenamedClassesDataCollector
     */
    private $renamedClassesDataCollector;
    public function __construct(ShortNameResolver $shortNameResolver, RenamedClassesDataCollector $renamedClassesDataCollector)
    {
        $this->shortNameResolver = $shortNameResolver;
        $this->renamedClassesDataCollector = $renamedClassesDataCollector;
    }
    public function shouldSkip(File $file, FullyQualifiedObjectType $fullyQualifiedObjectType, Node $node) : bool
    {
        // "new X" or "X::static()"
        /** @var array<string, string> $shortNamesToFullyQualifiedNames */
        $shortNamesToFullyQualifiedNames = $this->shortNameResolver->resolveFromFile($file);
        $removedUses = $this->renamedClassesDataCollector->getOldClasses();
        $fullyQualifiedObjectTypeShortName = $fullyQualifiedObjectType->getShortName();
        $className = $fullyQualifiedObjectType->getClassName();
        foreach ($shortNamesToFullyQualifiedNames as $shortName => $fullyQualifiedName) {
            if ($fullyQualifiedObjectTypeShortName !== $shortName) {
                $shortName = \strncmp($shortName, '\\', \strlen('\\')) === 0 ? \ltrim((string) Strings::after($shortName, '\\', -1)) : $shortName;
            }
            if ($fullyQualifiedObjectTypeShortName !== $shortName) {
                continue;
            }
            $fullyQualifiedName = \ltrim($fullyQualifiedName, '\\');
            if ($className === $fullyQualifiedName) {
                return \false;
            }
            if (\in_array($fullyQualifiedName, $removedUses, \true)) {
                return \false;
            }
            return \strpos($fullyQualifiedName, '\\') !== \false;
        }
        return \false;
    }
}
