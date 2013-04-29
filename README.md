ZfcDatagrid
===========

A datagrid for ZF2 where the data input and output can be whatever you want :-)

WORK IN PROGRESS....
Code will follow...


Features
===========
* different datasources: 
    * Zend\Sql\Select
    * Doctrine\ORM\QueryBuilder
    * [DONE] php arrays
    * ...
* output formats: 
    * [DONE] Bootstrap table
    * jqGrid
    * tcPDF
    * PHPExcel
    * ...
* [DONE] pagination
* [DONE] different column types and formatters
* styling the data output by column and/or value
* custom views/templates possible
* custom configuration
* extending the service
* ...TBD

Example
===========

```PHP
namespace MyModule\Controller;

class ExampleController extends AbstractActionController
{

    public function listAction ()
    {
        $queryBuilder = new \Doctrine\ORM\QueryBuilder();
        //@todo write the query
        
        $dataGrid = $this->getServiceLocator()->get('ZfcDatagrid');
        
        $dataGrid->setTitle('Title test');
        $dataGrid->setDataSource($queryBuilder);
        
        $col = new \ZfcDatagrid\Column\Standard('id', 'a');
        $dataGrid->addColumn($col);
        
        $col = new \ZfcDatagrid\Column\Standard('displayName', 'a');
        $col->setLabel('Displayname');
        $col->setWidth(50);
        $dataGrid->addColumn($col);
        
        $col = new \ZfcDatagrid\Column\Standard('familyName', 'a');
        $col->setLabel('Familyname');
        $col->setWidth(25);
        $dataGrid->addColumn($col);
        
        $col = new \ZfcDatagrid\Column\Standard('givenName', 'a');
        $col->setLabel('Givenname');
        $col->setWidth(25);
        $dataGrid->addColumn($col);
        
        $dataGrid->execute();

        return $dataGrid->getViewModel();
    }
}
```

Dependencies
===========
Required
--------
* PHP >= 5.3
* PHP intl extension
* ZF2
    * MVC (model, request, response)
    * Paginator
    * 
* Twitter Bootstrap (currently only output mode)

Optional
--------
* ZF2
    * translator
* Doctrine2 + DoctrineModule (if used as datasource)
* 
