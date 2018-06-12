<?php

namespace Flight\Service\Amadeus\Itinerary\Model\Itinerary;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Abstract Model, define functions used in all/most models
 *
 * @author    Michael Mueller <michael.mueller@invia.de>
 * @copyright Copyright (c) 2018  Invia Flights Germany GmbH
 */
abstract class AbstractModel
{
    /**
     * AbstractModel constructor.
     *
     * @param \stdClass|null $data
     */
    public function __construct(\stdClass $data = null)
    {
        if (null != $data) {
            $this->populate($data);
        }
    }

    /**
     * convert all entity fields into an array, recursive for 1:n and n:m relations.
     *
     * @param string $calledClass name of calling class on n:m relations to prevent circular dependency
     *
     * @return array
     */
    public function toArray($calledClass = null): array
    {
        $classMethods = get_class_methods(get_class($this));
        $methods      = [];
        foreach ($classMethods as $method) {
            if (substr($method, 0, 3) === 'get') {
                $methods[lcfirst(substr($method, 3))] = $method;
            }
        }
        $data = [];
        foreach ($methods as $key => $method) {
            if ($this->$method() instanceof AbstractModel) {
                $data[$key] = $this->$method()->toArray();
            } elseif ($this->$method() instanceof ArrayCollection) {
                $collectionData = [];
                foreach ($this->$method()->getValues() as $collectionModel) {
                    if (!is_string($collectionModel) && get_class($collectionModel) !== $calledClass) {
                        $collectionData[] = $collectionModel->toArray(get_class($this));
                    } else {
                        $collectionData[] = $collectionModel;
                    }
                }
                $data[$key] = $collectionData;
            } elseif ($this->$method() instanceof \DateTime) {
                $data[$key] = $this->$method()->format('d.m.Y H:i:s');
            } else {
                $data[$key] = $this->$method();
            }
        }
        $this->recursiveUnset($data, '__initializer__');
        $this->recursiveUnset($data, '__cloner__');
        $this->recursiveUnset($data, '__isInitialized__');

        return $data;
    }

    /**
     * removes the given key from given (multi-dimension) array.
     *
     * @param array  $array       array to remove key from
     * @param string $unwantedKey key to remove
     */
    public function recursiveUnset(&$array, $unwantedKey)
    {
        unset($array[$unwantedKey]);
        foreach ($array as &$value) {
            if (is_array($value)) {
                $this->recursiveUnset($value, $unwantedKey);
            }
        }
    }

    /**
     * @param \stdClass|null $data
     */
    abstract public function populate(\stdClass $data);
}
