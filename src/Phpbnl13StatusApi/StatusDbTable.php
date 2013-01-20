<?php

namespace Phpbnl13StatusApi;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Paginator\Adapter\DbSelect as DbTablePaginator;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class StatusDbTable extends AbstractTableGateway
{
    public function __construct(Adapter $adapter, $table = 'status')
    {
        $this->adapter            = $adapter;
        $this->table              = $table;
        $rowPrototype             = new Status();
        $hydratorPrototype        = new ClassMethodsHydrator();
        $this->resultSetPrototype = new HydratingResultSet($hydratorPrototype, $rowPrototype);
        $this->resultSetPrototype->buffer();
        $this->initialize();
    }

    public function fetchAll($user = null)
    {
        $select = $this->getSql()->select();
        $select->order('timestamp DESC');
        if ($user) {
            $select->where(array('user' => $user));
        }

        $adapter = new DbTablePaginator(
            $select, 
            $this->getAdapter(),
            $this->resultSetPrototype
        );
        $paginator = new Paginator($adapter);
        return $paginator;
    }
}
