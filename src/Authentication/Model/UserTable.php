<?php

namespace Authentication\Model;

use Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\AbstractTableGateway;

class UserTable extends AbstractTableGateway
{
    protected $table = 'auth_user';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new User());

        $this->initialize();
    }

    public function fetchAll()
    {
        $resultSet = $this->select();
        return $resultSet;
    }

    public function getUser($id)
    {
        $id = (int) $id;
        $rowset = $this->select(array(
            'id' => $id,
        ));

        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("User not found (#$id)");
        }

        return $row;
    }

    public function saveUser(User $user)
    {
        $data = array(
            'username' => $user->username,
            'password' => $user->password,
            'email' => $user->email,
            'is_super_user' => $user->is_super_user,
        );

        $id = (int) $user->id;

        if ($id == 0) {
            $this->insert($data);
        } elseif ($this->getUser($id)) {
            $this->update(
                $data,
                array(
                    'id' => $id,
                )
            );
        } else {
            throw new \Exception('Form id does not exist');
        }
    }

    public function deleteUser($id)
    {
        $this->delete(array(
            'id' => $id,
        ));
    }

    // todo add filters
}
