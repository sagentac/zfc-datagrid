<?php
namespace ZfcDatagrid\Column\DataPopulation;

use ZfcDatagrid\Column;

/**
 * Get the data from an external object.
 */
class DataObject implements DataPopulationInterface
{
    /** @var null|ObjectAwareInterface */
    private $object;

    /** @var array */
    private $objectParameters = [];

    /**
     * @param null|ObjectAwareInterface $object
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setObject(?ObjectAwareInterface $object): self
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @return null|ObjectAwareInterface
     */
    public function getObject(): ?ObjectAwareInterface
    {
        return $this->object;
    }

    /**
     * Apply a dynamic parameter based on row/column value.
     *
     * @param string                $objectParameterName
     * @param Column\AbstractColumn $column
     *
     * @return $this
     */
    public function addObjectParameterColumn(string $objectParameterName, Column\AbstractColumn $column): self
    {
        $this->objectParameters[] = [
            'objectParameterName' => $objectParameterName,
            'column'              => $column,
        ];

        return $this;
    }

    /**
     * @return array
     */
    public function getObjectParametersColumn(): array
    {
        return $this->objectParameters;
    }

    /**
     * Directly apply a "static" parameter.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setObjectParameter(string $name, $value): DataPopulationInterface
    {
        if ($this->getObject()) {
            $this->getObject()->setParameterFromColumn($name, $value);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function toString(): string | array
    {
        $return = '';
        if ($this->getObject()) {
            $return = $this->getObject()->toString();
        }

        return $return;
    }
}
