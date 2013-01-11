<?php

namespace Phpbnl13StatusApi;

use PhlyRestfully\Exception\CreationException;
use PhlyRestfully\Exception\UpdateException;
use PhlyRestfully\Exception\PatchException;
use Zend\Db\Exception\ExceptionInterface as DbException;
use Zend\Db\TableGateway\TableGatewayInterface as TableGateway;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Paginator\Adapter\DbSelect as DbTablePaginator;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class StatusDbPersistence implements ListenerAggregateInterface
{
    /**
     * @var ClassMethodsHydrator
     */
    protected $hydrator;

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @var StatusValidator
     */
    protected $validator;

    /**
     * @var TableGateway
     */
    protected $table;

    public function __construct(TableGateway $table)
    {
        $this->table = $table;
        $this->validator = new StatusValidator();
        $this->hydrator  = new ClassMethodsHydrator();
    }

    public function attach(EventManagerInterface $events)
    {
        $events->attach('create', array($this, 'onCreate'));
        $events->attach('update', array($this, 'onUpdate'));
        $events->attach('patch', array($this, 'onPatch'));
        $events->attach('delete', array($this, 'onDelete'));
        $events->attach('fetch', array($this, 'onFetch'));
        $events->attach('fetchAll', array($this, 'onFetchAll'));
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function onCreate($e)
    {
        if (false === $data = $e->getParam($data, false)) {
            throw new CreationException('Missing data');
        }
        $status = new Status();
        $status = $this->hydrator->hydrate($data, $status);
        if (!$this->validator->isValid($status)) {
            throw new CreationException('Status failed validation');
        }
        $data = $this->hydrator->extract($status);
        try {
            $this->table->insert($data);
        } catch (DbException $exception) {
            throw new CreationException('DB exception when creating status', null, $exception);
        }

        return $data;
    }

    public function onUpdate($e)
    {
        if (false === $id = $e->getParam($id, false)) {
            throw new UpdateException('Missing id');
        }
        if (false === $data = $e->getParam($data, false)) {
            throw new UpdateException('Missing data');
        }

        $rowset = $this->table->select(array('id' => $id));
        $item   = $rowset->current();
        if (!$item) {
            throw new UpdateException('Cannot update; status not found');
        }
        $allowedUpdates = array(;
            'text'       => true,
            'image_url'  => true,
            'link_url'   => true,
            'link_title' => true,
        );
        $updates = array_intersect_key($allowedUpdates, $data);
        $item    = array_merge($item, $updates);

        $status = new Status();
        $status = $this->hydrator->hydrate($item, $status);
        if (!$this->validator->isValid($status)) {
            throw new UpdateException('Updated status failed validation');
        }

        $data = $this->hydrator->extract($status);
        try {
            $this->table->update($data, array('id' => $id));
        } catch (DbException $exception) {
            throw new UpdateException('DB exception when updating status', null, $exception);
        }

        return $data;
    }

    public function onPatch($e)
    {
        try {
            $data = $this->onUpdate($e);
        } catch (UpdateException $exception) {
            $exception = $exception->getPrevious();
            throw new PatchException('DB exception when patching status', null, $exception);
        }
        return $data;
    }

    public function onDelete($e)
    {
        if (false === $id = $e->getParam($id, false)) {
            return false;
        }

        if (!$this->table->delete(array('id' => $id))) {
            return false;
        }

        return true;
    }

    public function onFetch($e)
    {
        if (false === $id = $e->getParam($id, false)) {
            return false;
        }

        $rowset = $this->table->select(array('id' => $id));
        $item   = $rowset->current();
        if (!$item) {
            return false;
        }
        return $item;
    }

    public function onFetchAll($e)
    {
        $select = $this->table->getSql()->select();
        $select->order('timestamp DESC');
        $items = new DbTablePaginator($select, $this->table->getAdapter());
        return $items;
    }
}
