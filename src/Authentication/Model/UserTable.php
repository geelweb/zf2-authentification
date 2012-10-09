<?php

namespace Authentication\Model;

use Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\AbstractTableGateway,
    Zend\Db\Sql\Expression;

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

    public function fetchAll($options=array())
    {
        $resultSet = $this->select();
        if (isset($options['mode_select']) && $options['mode_select']) {
            $return = array();
            foreach ($resultSet as $record) {
                $return[$record->id] = sprintf('%s <%s>', $record->username, $record->email);
            }
            return $return;
        }
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
            'password' => new Expression("crypt(?, gen_salt('md5'))", $user->password),
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
