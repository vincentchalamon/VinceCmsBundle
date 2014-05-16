<?php

/*
 * This file is part of the VinceCms bundle.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vince\Bundle\CmsBundle\Component;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Finder\Finder;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\Yaml\Yaml;
use Doctrine\Common\DataFixtures\AbstractFixture;

/**
 * Helps you to load your fixtures from yaml files
 *
 * @author Vincent CHALAMON <vincentchalamon@gmail.com>
 */
class YamlFixturesLoader
{

    /**
     * Files to load
     *
     * @var array
     */
    protected $files = array();

    /**
     * Loaded entities
     *
     * @var array
     */
    protected $entities = array();

    /**
     * Metadata objects
     *
     * @var array
     */
    protected $metas = array();

    /**
     * Referencer
     *
     * @var AbstractFixture
     */
    protected $referencer;

    /**
     * Manager
     *
     * @var ObjectManager
     */
    protected $manager;

    /**
     * Add yml fixtures file to load
     *
     * <code>
     * $loader->addFile('/path/to/fixtures.yml');
     * </code>
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     *
     * @param string $filename Yml filename
     *
     * @return YamlFixturesLoader Loader
     * @throws FileNotFoundException File doesn't exist
     * @throws \InvalidArgumentException File isn't yml extension
     */
    public function addFile($filename)
    {
        if (!is_file($filename)) {
            throw new FileNotFoundException(sprintf("File '%s' not found.", $filename));
        }
        if (pathinfo($filename, PATHINFO_EXTENSION) != 'yml') {
            throw new \InvalidArgumentException(sprintf("Invalid extension for file '%s'.", $filename));
        }

        if (!in_array($filename, $this->files)) {
            $this->files[] = $filename;
        }

        return $this;
    }

    /**
     * Add directory with yml files to load
     *
     * <code>
     * $loader->addDirectory('/path/to/fixtures');
     * $loader->addDirectory('/path/to/fixtures', false);
     * </code>
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     *
     * @param string  $path      Path name with yml files
     * @param boolean $recursive Read path recursively
     *
     * @return YamlFixturesLoader Loader
     * @throws FileNotFoundException File doesn't exist
     */
    public function addDirectory($path, $recursive = true)
    {
        if (realpath($path) === false || !is_dir(realpath($path))) {
            throw new FileNotFoundException(sprintf("Directory '%s' not found.", $path));
        }

        $finder = new Finder();
        $finder->files()->name('*.yml')->depth($recursive ? '>= 0' : '== 0')->in($path)->sortByName();
        foreach ($finder as $file) {
            /** @var \SplFileInfo $file */
            $this->addFile($file->getRealpath());
        }

        return $this;
    }

    /**
     *
     * Load fixtures
     *
     * <code>
     * $loader->load($manager);
     * $loader->load($manager, function ($name, $entity){
     *     $entity->myFunction();
     * });
     * $loader->load($manager, function ($name, $entity){
     *     $entity->myFunction();
     * }, $this);
     * </code>
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param ObjectManager   $manager    Manager
     * @param callable        $callback   Callback
     * @param AbstractFixture $referencer Fixtures class for referencing entities
     *
     * @throws \InvalidArgumentException Invalid class name
     */
    public function load(ObjectManager $manager, \Closure $callback = null, AbstractFixture $referencer = null)
    {
        $this->manager = $manager;
        if ($referencer) {
            $this->referencer = $referencer;
        }

        // Parse each file
        foreach ($this->files as $file) {
            $models = Yaml::parse($file);
            if (!$models) {
                throw new \InvalidArgumentException(sprintf("File '%s' has no fixtures.", $file));
            }
            foreach ($models as $class => $entities) {
                if (!class_exists($class)) {
                    throw new \InvalidArgumentException(sprintf("Class '%s' does not exist in file '%s'.", $class, $file));
                }
                if (!$entities) {
                    throw new \InvalidArgumentException(sprintf("Class '%s' has no fixtures in file '%s'.", $class, $file));
                }
                foreach ($entities as $name => $entity) {
                    $this->getManager()->persist($this->buildEntity($name, $this->getValidClassName($class), $entity, $callback));
                }

                // Flush
                $this->getManager()->flush();
            }
        }
    }

    /**
     * Build an entity
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     *
     * @param string   $name     Entity name for fixtures relations
     * @param string   $class    Entity class name
     * @param array    $values   Entity values indexed by columns name
     * @param \Closure $callback Callback
     *
     * @return object Entity
     * @throws \InvalidArgumentException Invalid type for relation multiple
     */
    protected function buildEntity($name, $class, array $values, \Closure $callback = null)
    {
        if (!isset($this->metas[$class])) {
            $this->metas[$class] = $this->getManager()->getClassMetadata($class);
        }
        /** @var ClassMetadata $meta */
        $meta = $this->metas[$class];

        // Build new object
        $record = new $class();
        foreach ((array)$values as $column => $value) {
            // Association
            if ($meta->hasAssociation($column)) {
                $mapping     = $meta->getAssociationMapping($column);
                $objectClass = $meta->getAssociationTargetClass($column);
                if (!isset($this->metas[$objectClass])) {
                    $this->metas[$objectClass] = $this->getManager()->getClassMetadata($objectClass);
                }
                /** @var ClassMetadata $objectMeta */
                $objectMeta = $this->metas[$objectClass];

                // OneToMany/ManyToMany
                if ($meta->isCollectionValuedAssociation($column)) {
                    // Values must be an array
                    if (!is_array($value)) {
                        throw new \InvalidArgumentException(sprintf("You must specify an array for association '%s' on entity '%s' for class '%s'.", $column, $name, $class));
                    }

                    // Create or retrieve entities
                    foreach ($value as $objectKey => $objectValue) {
                        // Is entity already created ?
                        if (is_string($objectValue)) {
                            $object = $this->getEntity($objectClass, $objectValue);
                        } else {
                            $object = $this->buildEntity($objectKey, $objectClass, $objectValue, $callback);

                            // todo-vince Seems there is a bug on Doctrine2 cascade persist: need to call setter on bidirectional associations
                            if (isset($mapping['mappedBy'])) {
                                if (is_callable(array($object, 'set'.Inflector::classify($mapping['mappedBy'])))) {
                                    call_user_func(array($object, 'set'.Inflector::classify($mapping['mappedBy'])), $record);
                                } else {
                                    $objectMeta->getReflectionProperty($mapping['mappedBy'])->setValue($object, $record);
                                }
                            }

                            // Need to persist sub-object
                            $this->getManager()->persist($object);
                        }

                        // Set object value
                        if (is_callable(array($record, 'add'.Inflector::classify($objectMeta->getReflectionClass()->getShortName())))) {
                            call_user_func(array($record, 'add'.Inflector::classify($objectMeta->getReflectionClass()->getShortName())), $object);
                        } elseif (is_callable(array($record, 'add'.Inflector::classify($column)))) {
                            call_user_func(array($record, 'add'.Inflector::classify($column)), $object);
                        } else {
                            $meta->getReflectionProperty($column)->getValue($record)->add($object);
                        }
                    }

                // ManyToOne/OneToOne
                } else {
                    // Is entity already created ?
                    if (is_string($value)) {
                        $object = $this->getEntity($objectClass, $value);
                    } else {
                        $object = $this->buildEntity("$name-$column", $objectClass, $value);

                        // Need to persist sub-object
                        $this->getManager()->persist($object);
                    }

                    // Set object value
                    if (is_callable(array($record, 'set'.Inflector::classify($column)))) {
                        call_user_func(array($record, 'set'.Inflector::classify($column)), $object);
                    } else {
                        $meta->getReflectionProperty($column)->setValue($record, $object);
                    }
                }

            // Property
            } elseif ($meta->hasField($column)) {
                $type = Type::getType($meta->getTypeOfField($column));
                if (strtolower($type) != 'array') {
                    $value = $type->convertToPHPValue($value, $this->getManager()->getConnection()->getDatabasePlatform());
                }

                // Set property value
                if (is_callable(array($record, 'set'.Inflector::classify($column)))) {
                    call_user_func(array($record, 'set'.Inflector::classify($column)), $value);
                } else {
                    $meta->getReflectionProperty($column)->setValue($record, $value);
                }

            // Custom call
            } elseif (is_callable(array($record, 'set'.Inflector::classify($column)))) {
                call_user_func(array($record, 'set'.Inflector::classify($column)), $value);

            } else {
                throw new \InvalidArgumentException(sprintf("Undefined property or association %s on entity '%s' for class '%s'. Maybe you forgot to create custom setter 'set%s' ?", $column, $name, $class, Inflector::classify($column)));
            }
        }

        // Callback
        if ($callback) {
            $callback($name, $record);
        }

        // Add reference
        $this->entities[$class][$name] = $record;
        if ($this->hasReferencer()) {
            $count     = 1;
            $reference = $this->getValidClassName($class)."-$name";
            while ($this->getReferencer()->hasReference($reference)) {
                $reference = $this->getValidClassName($class)."-$name-$count";
                $count++;
            }
            $this->getReferencer()->addReference($reference, $record);
        }

        return $record;
    }

    /**
     * Get existing entity by reference or same file loading
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     *
     * @param string $class Class name
     * @param string $name  Entity name
     *
     * @return object Entity
     * @throws \Exception No entity found
     */
    protected function getEntity($class, $name)
    {
        if (isset($this->entities[$class][$name])) {
            return $this->entities[$class][$name];
        } elseif ($this->hasReferencer() && $this->getReferencer()->hasReference($name)) {
            return $this->getManager()->merge($this->getReferencer()->getReference($name));
        }
        throw new \Exception(sprintf("No entity found for name '%s'.", $name));
    }

    /**
     * Validate classname for entity.
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     *
     * @param string $name Class name
     *
     * @return string
     */
    protected function getValidClassName($name)
    {
        return $this->getManager()->getClassMetadata($name)->getName();
    }

    /**
     * Get manager object
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     * @return ObjectManager
     */
    protected function getManager()
    {
        return $this->manager;
    }

    /**
     * Check if referencer has been specified on load
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     * @return boolean
     */
    protected function hasReferencer()
    {
        return $this->referencer ? true : false;
    }

    /**
     * Get referencer object if it has been specified on load
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     * @return AbstractFixture
     */
    protected function getReferencer()
    {
        return $this->referencer;
    }
}