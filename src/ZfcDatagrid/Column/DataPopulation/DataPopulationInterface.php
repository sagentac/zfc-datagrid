<?php
namespace ZfcDatagrid\Column\DataPopulation;

interface DataPopulationInterface
{
    /**
     * Return the result.
     *
     * @return string
     */
    public function toString(): string | array;

    /**
     * Directy set a parameter for the object.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setObjectParameter(string $name, $value): self;

    /**
     * @return array
     */
    public function getObjectParametersColumn(): array;
}
