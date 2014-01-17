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
use Doctrine\ORM\EntityManager;
use Symfony\Component\Finder\Finder;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\Yaml\Yaml;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\ConstraintViolation;

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
     * Validator
     *
     * @var Validator
     */
    protected $validator;

    /**
     * Add yml fixtures file to load
     *
     * <code>
     * $loader->addFile(__DIR__.'/../../Resources/data/fixtures.yml');
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
        if (pathinfo($filename, PATHINFO_EXTENSION) != "yml") {
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
     * $loader->addDirectory(__DIR__.'/../../Resources/data');
     * $loader->addDirectory(__DIR__.'/../../Resources/data', false);
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
     * $loader->load($manager, function ($name, $entity){
     *     $entity->myFunction();
     * }, $this, $this->container->get('validator'));
     * </code>
     *
     * @author Vincent Chalamon <vincentchalamon@gmail.com>
     *
     * @param ObjectManager   $manager    Manager
     * @param callable        $callback   Callback
     * @param AbstractFixture $referencer Fixtures class for referencing entities
     * @param Validator       $validator  Validator service to validate entities
     *
     * @throws \InvalidArgumentException Invalid class name
     */
    public function load(ObjectManager $manager, \Closure $callback = null, AbstractFixture $referencer = null, Validator $validator = null)
    {
        $this->manager = $manager;
        if ($validator) {
            $this->validator = $validator;
        }
        if ($referencer) {
            $this->referencer = $referencer;
        }

        // Parse each file
        foreach ($this->files as $file) {
            $models = Yaml::parse($file);
            if ($models) {
                foreach ($models as $class => $entities) {
                    if ($entities) {
                        foreach ($entities as $name => $entity) {
                            $this->buildEntity($name, $this->getValidClassName($class), $entity);
                        }
                    } else {
                        throw new \InvalidArgumentException(sprintf('Class "%s" has no fixtures in file "%s".', $class, $file));
                    }
                }
            } else {
                throw new \InvalidArgumentException(sprintf('File "%s" has no fixtures.', $file));
            }
        }

        // Callback by entity
        if ($callback) {
            foreach ($this->entities as $name => $entity) {
                $callback($name, $entity);
            }
        }
        $this->getManager()->flush();

        // Add references
        if ($this->hasReferencer()) {
            foreach ($this->entities as $class => $entities) {
                foreach ($entities as $name => $entity) {
                    $this->getReferencer()->addReference($this->getValidClassName($class)."-$name", $entity);
                }
            }
        }
    }

    /**
     * Build an entity
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     *
     * @param string $name   Entity name for fixtures relations
     * @param string $class  Entity class name
     * @param array  $values Entity values indexed by columns name
     *
     * @return object Entity
     * @throws \Exception Invalid type for relation multiple
     */
    protected function buildEntity($name, $class, array $values)
    {
        if (!isset($this->metas[$class])) {
            $this->metas[$class] = $this->getManager()->getClassMetadata($class);
        }

        // Build new object
        $record = new $class();
        foreach ((array)$values as $column => $value) {
            // Relation with ClassMetadata
            if ($this->metas[$class]->hasAssociation($column)) {
                $objectClass = $this->metas[$class]->getAssociationTargetClass($column);
                $mapping     = $this->metas[$class]->getAssociationMapping($column);
                if (!isset($this->metas[$objectClass])) {
                    $this->metas[$objectClass] = $this->getManager()->getClassMetadata($objectClass);
                }

                // Multiple relation
                if ($this->metas[$class]->isCollectionValuedAssociation($column)) {
                    // Values must be an array
                    if (!is_array($value)) {
                        throw new \Exception(sprintf("You must specify an array for relation '%s'.", $column));
                    }

                    // Create or retrieve entities
                    foreach ($value as $objectKey => $objectValue) {
                        // Is entity already created ?
                        if (is_string($objectValue)) {
                            $object = $this->getEntity($objectClass, $objectValue);
                        } else {
                            $object = $this->buildEntity($objectKey, $objectClass, $objectValue);
                        }

                        // Set object value
                        if (is_callable(array($record, $this->buildMethod("add", $this->metas[$objectClass]->getReflectionClass()->getShortName())))) {
                            call_user_func(array($record, $this->buildMethod("add", $this->metas[$objectClass]->getReflectionClass()->getShortName())), $object);
                        } else {
                            $this->metas[$class]->getReflectionProperty($column)->getValue($record)->add($object);
                        }

                        // Set target object inversed relation value if needed
                        if (isset($mapping['mappedBy'])) {
                            if (is_callable(array($object, $this->buildMethod("set", $mapping['mappedBy'])))) {
                                call_user_func(array($object, $this->buildMethod("set", $mapping['mappedBy'])), $record);
                            } else {
                                $this->metas[$objectClass]->getReflectionProperty($mapping['mappedBy'])->setValue($object, $record);
                            }
                        }
                    }
                } else {
                    // Is entity already created ?
                    if (is_string($value)) {
                        $object = $this->getEntity($objectClass, $value);
                    } else {
                        $object = $this->buildEntity("$name-$column", $objectClass, $value);
                    }

                    // Set object value
                    if (is_callable(array($record, $this->buildMethod("set", $column)))) {
                        call_user_func(array($record, $this->buildMethod("set", $column)), $object);
                    } else {
                        $this->metas[$class]->getReflectionProperty($column)->setValue($record, $object);
                    }

                    // Set target object inversed relation value if needed
                    if (isset($mapping['inversedBy'])) {
                        if ($this->metas[$objectClass]->isSingleValuedAssociation($mapping['inversedBy'])) {
                            if (is_callable(array($object, $this->buildMethod("set", $mapping['inversedBy'])))) {
                                call_user_func(array($object, $this->buildMethod("set", $mapping['inversedBy'])), $record);
                            } else {
                                $this->metas[$objectClass]->getReflectionProperty($mapping['inversedBy'])->setValue($object, $record);
                            }
                        } else {
                            if (is_callable(array($object, $this->buildMethod("add", $this->metas[$objectClass]->getReflectionClass()->getShortName())))) {
                                call_user_func(array($object, $this->buildMethod("add", $this->metas[$objectClass]->getReflectionClass()->getShortName())), $record);
                            } else {
                                $this->metas[$objectClass]->getReflectionProperty($mapping['inversedBy'])->getValue($object)->add($record);
                            }
                        }
                    }
                }
                // Column with ClassMetadata
            } else {
                if ($this->metas[$class]->hasField($column)) {
                    $type = Type::getType($this->metas[$class]->getTypeOfField($column));
                    if (strtolower($type) != 'array') {
                        $value = $type->convertToPHPValue($value, $this->getManager()->getConnection()->getDatabasePlatform());
                    }
                }
                if (is_callable(array($record, $this->buildMethod("set", $column)))) {
                    call_user_func(array($record, $this->buildMethod("set", $column)), $value);
                } elseif ($this->metas[$class]->hasField($column)) {
                    $this->metas[$class]->getReflectionProperty($column)->setValue($record, $value);
                } else {
                    throw new \InvalidArgumentException(sprintf("Unknown method '%s' on '%s' object (%s).", $this->buildMethod("set", $column), $name, $class));
                }
            }
        }
        if ($this->validator) {
            $errors = $this->validator->validate($record);
            foreach ($errors as $error) {
                /** @var ConstraintViolation $error */
                throw new \InvalidArgumentException(sprintf(<<<EOF
Fail to validate field "%s" on entity "%s" for class "%s":
%s
EOF
                    , $error->getPropertyPath()
                    , $name
                    , $class
                    , $error->getMessage()));
            }
        }
        $this->getManager()->persist($record);
        $this->entities[$class][$name] = $record;

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
     * Build method for adder or setter
     *
     * @author Vincent CHALAMON <vincentchalamon@gmail.com>
     *
     * @param string $method Method name
     * @param string $class  Class name
     *
     * @return string Method
     */
    protected function buildMethod($method, $class)
    {
        return $method.Inflector::classify($class);
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
     * @return EntityManager
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