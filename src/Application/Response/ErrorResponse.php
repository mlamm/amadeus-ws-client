<?php
namespace AmadeusService\Application\Response;

use AmadeusService\Application\Exception\ServiceException;
use Doctrine\Common\Collections\ArrayCollection;

class ErrorResponse extends HalResponse
{
    /**
     * @var ArrayCollection
     */
    protected $violations;

    /**
     * ErrorResponse constructor.
     *
     * @param null $data
     * @param int $status
     * @param array $headers
     * @param bool $json
     */
    public function __construct($data = null, $status = 500, array $headers = array(), $json = false)
    {
        parent::__construct($data, $status, $headers, $json);
        $this->violations = new ArrayCollection();
    }

    /**
     * Add a new violation referenced to an entity.
     *
     * @param string $entity
     * @param ServiceException $exception
     * @return $this
     */
    public function addViolation($entity, ServiceException $exception)
    {
        if ($this->violations->get($entity) === null) {
            $this->violations->set($entity, []);
        }

        /** @var array $currentEntity */
        $currentEntity = $this->violations->get($entity);
        array_push($currentEntity, $exception);

        $this->violations->set($entity, $currentEntity);

        $violations = new \ArrayObject();
        $violations->offsetSet('errors', $this->violations->toArray());

        $this->setData($violations);
        $this->update();

        return $this;
    }
}