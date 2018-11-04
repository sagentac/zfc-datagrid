<?php
namespace ZfcDatagrid\DataSource;

use Zend\Paginator\Adapter\AdapterInterface as PaginatorAdapterInterface;
use ZfcDatagrid\Column;
use ZfcDatagrid\Filter;

abstract class AbstractDataSource implements DataSourceInterface
{
    /**
     * @var Column\AbstractColumn[]
     */
    protected $columns = [];

    /**
     * @var array
     */
    protected $sortConditions = [];

    /**
     * @var Filter[]
     */
    protected $filters = [];

    /**
     * The data result.
     *
     * @var PaginatorAdapterInterface|null
     */
    protected $paginatorAdapter;

    /**
     * Set the data source
     * - array
     * - ZF2: Zend\Db\Sql\Select
     * - Doctrine2: Doctrine\ORM\QueryBuilder
     * - ...
     *
     * @param mixed $data
     *
     * @throws \Exception
     */
    public function __construct($data)
    {
        // we need this exception, because a abstract __construct, create a exception in php-unit for mocking
        throw new \Exception(sprintf('Missing __construct in %s', get_class($this)));
    }

    /**
     * Set the columns.
     *
     * @param Column\AbstractColumn[] $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return Column\AbstractColumn[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Set sort conditions.
     *
     * @param Column\AbstractColumn $column
     * @param string                $sortDirection
     */
    public function addSortCondition(Column\AbstractColumn $column, $sortDirection = 'ASC')
    {
        $this->sortConditions[] = [
            'column'        => $column,
            'sortDirection' => $sortDirection,
        ];
    }

    /**
     * @param array $sortConditions
     */
    public function setSortConditions(array $sortConditions)
    {
        $this->sortConditions = $sortConditions;
    }

    /**
     * @return array
     */
    public function getSortConditions(): array
    {
        return $this->sortConditions;
    }

    /**
     * Add a filter rule.
     *
     * @param Filter $filter
     */
    public function addFilter(Filter $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * @param Filter[] $filters
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param PaginatorAdapterInterface|null $paginator
     */
    public function setPaginatorAdapter(?PaginatorAdapterInterface $paginator)
    {
        $this->paginatorAdapter = $paginator;
    }

    /**
     * @return PaginatorAdapterInterface
     */
    public function getPaginatorAdapter(): ?PaginatorAdapterInterface
    {
        return $this->paginatorAdapter;
    }

    /**
     * Get the data back from construct.
     *
     * @return mixed
     */
    abstract public function getData();

    /**
     * Execute the query and set the paginator
     * - with sort statements
     * - with filters statements.
     */
    abstract public function execute();
}
