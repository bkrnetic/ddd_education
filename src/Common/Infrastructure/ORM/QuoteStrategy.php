<?php

namespace Common\Infrastructure\ORM;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\QuoteStrategy as DoctrineQuoteStrategy;

class QuoteStrategy implements DoctrineQuoteStrategy
{
    private function quote(string $token, AbstractPlatform $platform): string
    {
        switch ($platform->getName()) {
            case 'postgresql':
                return sprintf('"%s"', $token);
            default:
                return $token;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnName($fieldName, ClassMetadata $class, AbstractPlatform $platform): string
    {
        return $this->quote($class->fieldMappings[$fieldName]['columnName'], $platform);
    }

    /**
     * {@inheritdoc}
     */
    public function getTableName(ClassMetadata $class, AbstractPlatform $platform): string
    {
        return $class->table['name'];
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed[] $definition
     */
    public function getSequenceName(array $definition, ClassMetadata $class, AbstractPlatform $platform): string
    {
        return $definition['sequenceName'];
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed[] $joinColumn
     */
    public function getJoinColumnName(array $joinColumn, ClassMetadata $class, AbstractPlatform $platform): string
    {
        return $joinColumn['name'];
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed[] $joinColumn
     */
    public function getReferencedJoinColumnName(array $joinColumn, ClassMetadata $class, AbstractPlatform $platform): string
    {
        return $joinColumn['referencedColumnName'];
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed[] $association
     */
    public function getJoinTableName(array $association, ClassMetadata $class, AbstractPlatform $platform): string
    {
        return $association['joinTable']['name'];
    }

    /**
     * {@inheritdoc}
     *
     * @return mixed[]
     */
    public function getIdentifierColumnNames(ClassMetadata $class, AbstractPlatform $platform)
    {
        return $class->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnAlias($columnName, $counter, AbstractPlatform $platform, ?ClassMetadata $class = null): string
    {
        return $platform->getSQLResultCasing($columnName . '_' . $counter);
    }
}
