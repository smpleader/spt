<?php
/**
 * SPT software - Model
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Just a basic model
 * 
 */
namespace SPT;

use SPT\Log; 
use SPT\Query; 
use SPT\Support\FncNumber;
use SPT\Support\FncDatetime;
use SPT\Support\FncString;
use SPT\User\Instance as UserInstance;

class Entity
{
    protected $db; 
    protected $table;
    protected $pk; 

    public function __construct(Query $query, UserInstance $user, array $options = [])
    {
        $this->db = $query;
        $this->user = $user;
    }

    public function logs()
    {
        return $this->db->getLog();
    }

    public function getFields()
    {
        return [];
    }

    public function findOne($where, $select = '*')
    {
        return $this->db->table( $this->table )->detail($where, $select );
    }
    
    public function findByPK( $pk, $select = '*')
    {
        return $this->findOne( [$this->pk => $pk ], $select );
    }

    public function fill(array $data = [] )
    {
        // fill data for the default fields
        return $data;
    }

    public function validate( $data )
    {
        // validate data
        return $data;
    }

    public function add( $data, $where = [])
    {
        if (!$this->validate($data))
        {
            return false;
        }
        if( !count($where) )
        {
            if( isset($data[$this->pk]) )
            {
                $where = [$this->pk => $data[$this->pk] ];
            }
        }

        $data = $this->fill( $data );

        return count($where) ? 
            $this->db->table( $this->table )->insertOnce($data, $where) :
            $this->db->table( $this->table )->insert($data);
    }

    public function update( $data, $where = [])
    {
        if (!$this->validate($data))
        {
            return false;
        }
        if( !count($where) )
        {
            if( !isset($data[$this->pk]) )
            {
                $this->error = 'Updated failed because of PK not found';
                return false;
            }

            $where = [$this->pk => $data[$this->pk] ];
        }

        $result = false;
        $exist = $this->findOne($where);

        if( empty($exist) )
        {
            $this->error = 'Updated failed because of record not found';
            return false;
        }

        return $this->db->table( $this->table )->update($data, $where);
    }

    public function remove( $id )
    {   
        return $this->db->table( $this->table )->delete( [$this->pk => $id ] );
    }

    public function list( $start, $limit, array $where = [], $order = '', $select = '*')
    {
        $list = $this->db->select( $select )->table( $this->table );

        if( count($where) )
        {
            $list->where( $where );
        }

        if($order)
        {
            $list->orderby($order);
        }

        return $list->countTotal(true)->list( $start, $limit);
    }

    public function getListTotal()
    {
        return $this->db->total();
    }

    public function truncate()
    {
        return $this->db->table( $this->table )->truncate();
    }

    public function column(array $list, string $col, $allowEmpty = [])
    {
        $res = [];
        foreach($list as $row)
        {
            if( count( $allowEmpty ) )
            {
                if( in_array($row[$col], $allowEmpty) )
                {
                    $res[] = $row[$col];
                }
            }
            else
            {
                $tmp = trim( $row[$col] );
                if( !empty($tmp) )
                {
                    $res[] = $tmp;
                }
            }
        }
        return $res;
    }

    public function checkAvailability()
    {
        
        $fields_db = $this->db->table($this->table)->structureTable();
        $fields = $this->getFields();

        //check type fields in database and update type fields
        $query = [];
        $pk = [];
        foreach($fields as $key => $value)
        {
            $query[$key] = "";
            $check = false;
            $type = "";
            $is_null = (isset($value['null']) && $value['null']) ? $value['null'] : "NO";
            $default_value = (isset($value['default_value']) && $value['default_value']) ? $value['default_value'] : "";
            $extra = (isset($value['extra']) && $value['extra']) ? $value['extra'] : "";

            if (isset($value['pk']) && $value['pk'])
            {
                $pk[] = $key;
            }  

            if (isset($value['limit']) && $value['limit'])
            {
                $type = $value['type'] .'('. $value['limit'] .')';
            }
            else
            {
                $type = $value['type'];
            }
            if (isset($value['option']) && $value['option'])
            {
                $type .= " ". $value['option'];
            }     

            if ($fields_db)
            {
                foreach ($fields_db as $field)
                {
                    if ($key == $field['Field'])
                    {
                        $check = true;
                        if ($type != $field['Type'] || $is_null != $field['Null'] || $default_value != $field['Default'] || $extra != $field['Extra'])
                        {
                            $query[$key] = 'MODIFY '. $key. ' '. $type ;
                            $query[$key] .= $is_null == "NO" ? " NOT NULL" : " NULL";
                            $query[$key] .= $default_value ? " DEFAULT '". $default_value. "' " : "";
                            $query[$key] .= $extra ? " ". $extra : "";
                        }
                    }
                }

                if (!$check)
                {
                    $query[$key] = 'ADD '. $key. ' '. $type ;
                    $query[$key] .= $is_null == "NO" ? " NOT NULL" : " NULL";
                    $query[$key] .= $default_value ? " DEFAULT '". $default_value. "' " : "";
                    $query[$key] .= $extra ? " ". $extra : "";
                }
            }
            else
            {
                $query[$key] = "\n `". $key. "` " . $type ;
                $query[$key] .= $is_null == "NO" ? " NOT NULL" : " NULL";
                $query[$key] .= $default_value ? " DEFAULT '". $default_value. "' " : "";
                $query[$key] .= $extra ? " ". $extra : "";
            }
            
        }

        $pk_db = [];
        if ($fields_db)
        {
            foreach ($fields_db as $field)
            {
                if ($field['Key'] == "PRI")
                {
                    $pk_db[] = $field['Field'];
                }

                if (!array_key_exists($field['Field'], $query))
                {
                    $query[$field['Field']] = "DROP COLUMN ". $field['Field']; 
                }
            }
        }
        
        $fields_build = array_filter(array_values($query));

        $update_pk = false;
        foreach($pk_db as $item)
        {
            if (!in_array($item, $pk))
            {
                $update_pk = true;
                break;
            }
        }

        if (($update_pk || count($pk) != count($pk_db)) && $fields_db)
        {
            $pk = implode(",", $pk);
            $fields_build[] = 'DROP PRIMARY KEY';
            $fields_build[] = 'ADD PRIMARY KEY('. $pk .')';
        }
        elseif (!$fields_db)
        {
            $pk = implode(",", $pk);
            $fields_build[] = 'PRIMARY KEY('. $pk . ')';
        }
 
        if (count($fields_build))
        {
            if( $fields_db )
            {
                return $this->db->alterTable($fields_build, $this->table);
            }
            else
            {
                return $this->db->createTable($fields_build, $this->table);
            }
        }
        
        return true;
    }

    public function bind($data = [], $returnObject = true)
    {
        $row = [];
        $data = (array) $data;
        $fields = $this->getFields();
        foreach ($fields as $key => $field)
        {
            $default = isset($field['default']) ? $field['default'] : '';
            $row[$key] = isset($dat[$key]) ? $dat[$key] : $default;
        }

        return $returnObject ? (object)$row : $row;
    }

    public function autoGenerate(int $length = 1, $callBack = null)
    {
        $fields = $this->getFields();

        $data = [];
        for ($i=0; $i < $length; $i++) 
        { 
            $row = [];
            foreach ($fields as $key => $field)
            {
                $row[$key] = null === $callBack ? $this->radomize($field) : $callBack($field);
            }

            $data[] = (object) $row;
        }
        
        return $data;
    }

    public function radomize(array $field)
    {
        $arr = [
            'varchar' => 'string', 'text'  => 'string', 'char' => 'string', 'mediumtext' => 'string',
            'int' => 'int', 'long' => 'int', 'tinyint' => 'int', 'smailint' => 'int', 'decimal' => 'int', 'float' => 'int', 'double' => 'int',
            'date' => 'datetime', 'datetime' => 'datetime', 'time' => 'datetime', 'year' => 'datetime', 'timestamp' => 'datetime'
        ];

        switch($arr[$field['type']])
        {
            case 'string': 
                return FncString::radomize($field['limit']);
            case 'int': 
                $absolute = isset($field['option']) && ($field['option'] == 'unsigned');
                return FncNumber::radomize($field['type'], $absolute); 
            case 'datetime': 
                return FncDatetime::radomize($field['type']);
        }
    }
}
