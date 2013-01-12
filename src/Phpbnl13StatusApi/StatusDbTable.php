<?php

namespace Phpbnl13StatusApi;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class StatusDbTable extends AbstractTableGateway
{
    public function __construct(Adapter $adapter, $table = 'status')
    {
        $this->adapter           = $adapter;
        $this->table             = $table;
        $rowPrototype            = new Status();
        $hydratorPrototype       = new ClassMethodsHydrator();
        $this->resultSetProtoype = new HydratingResultSet($hydratorPrototype, $rowPrototype);
        $this->initialize();
    }
}
