<?php
/**
 * SPT software - Query class, wrap db.class for simpler and more advanced usage
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a query builder
 * 
 */

namespace SPT;

class Query
{
    protected $db;
    protected $query;

    protected $prefix;
    protected $qq;
    protected $table;
    protected $fields;
    protected $join;
    protected $where;
    protected $value;
    protected $orderby;
    protected $groupby;
    protected $limit;
    protected $countTotal;
    protected $total;

    public function __construct(PDOWrapper $db, $prefix, $fquota='`')
    {
        $this->db = $db;
        $this->prefix = $prefix;
        $this->qq = $fquota;
        $this->reset();
    }

    protected function reset()
    {
        $this->query = '';
        $this->table = '';
        $this->fields = [];
        $this->join = [];
        $this->where = [];
        $this->value = [];
        $this->orderby = '';
        $this->groupby = '';
        $this->limit = '';
        $this->countTotal = false;
        $this->total = 0;
    }

    protected function prefix($q)
    {
        if(FncArray::ifReady($this->prefix))
        {
            foreach($this->prefix as $find=>$replace)
            {
                $q = str_replace($find, $replace, $q);
            }
        }
        return $q;
    }

    protected function qq($name)
    {
        if(  
            strpos($name, $this->qq) === false 
            && strpos($name, ' as ') === false
            && strpos($name, '(') === false
            && strpos($name, ' ') !== 0
        )
        {
            $name = $this->qq. $name .$this->qq;
        }

        if( strpos($name, $this->qq) === 0 && 
            strpos($name, $this->qq.'.'.$this->qq) === false)
        {
            $name = str_replace('.', $this->qq.'.'. $this->qq, $name);
        }

        return $name;
    }

    public function isConnected()
    {
        return $this->db->connected;
    }

    public function total()
    {
        return $this->total;
    }

    public function countTotal($boolean = null)
    {
        if( null === $boolean) return $this->countTotal;
        $this->countTotal = $boolean;
    }

    public function table($name)
    {
        $this->table = $this->qq($this->prefix($name));
        return $this;
    }

    public function select($fields)
    {
        if(FncArray::ifReady($fields))
        {
            $this->fields[] = $this->qq. implode($this->qq. ','.$this->qq, $fields).$this->qq;
        }
        elseif(is_string($fields))
        {
            if( strpos($fields, '*') === false && strpos($fields, ',') === false )
            {
                $fields = $this->qq($fields);
            }

            $this->fields[] = $fields;
        }
  
        return $this;
    }


    /*public function where($conditions){

        if(FncArray::ifReady($conditions))
        {
            foreach($conditions as $key=>$val)
            {
                
                if(is_array($val))
                {
                    $ws = stripos($val[0], 'LIKE') === false ? $this->qq($key). ' '. $val[0]. ' ?' :  $this->qq($key). ' '. $val[0]. ' %?%';
                    $this->value($val[1]);
                }
                elseif(is_numeric($key))
                {
                    $ws = $val;
                }
                else
                {
                    $ws = $this->qq($key).' = ?';
                    $this->value($val);
                }

                $this->where($ws);
            }
        }
        elseif(is_string($conditions))
        {
            $this->where[] = $conditions;
        }
  
        return $this;
    }*/

    public function value($value, $place="where"){

        if(is_object($value) || is_array($value))
        {
            foreach($value as $val)
            {
                $this->value($val, $place);
            }
        }
        else
        {
            $this->value[$place][] = $value;
        }
  
        return $this;
    }

    public function getValue()
    {
        $arr = [];
        foreach(['update', 'insert', 'where'] as $place)
        {
            if(isset($this->value[$place])){
                foreach($this->value[$place] as $value)
                {
                    $arr[] = $value;
                }
            }
        }
        
        return $arr;
    }

    public function orderby($order){

        if(FncArray::ifReady($order))
        {
            $this->orderby = implode(' ', $order);
        }
        elseif(is_string($order))
        {
            $this->orderby = $order;
        }
  
        return $this;
    }

    public function groupby($group){

        if(FncArray::ifReady($group))
        {
            $this->groupby = implode(', ', $group);
        }
        elseif(is_string($group))
        {
            $this->groupby = $group;
        }
  
        return $this;
    }

    public function limit($limit){

        if(FncArray::ifReady($limit))
        {
            $this->limit = implode(', ', $limit);
        }
        elseif(is_string($limit) || is_numeric($limit))
        {
            $this->limit = $limit;
        }
  
        return $this;
    }

    public function join($joins){

        if(FncArray::ifReady($joins))
        {
            foreach($joins as $j)
            {
                if(is_string($j))
                {
                    $this->join[] = $j;
                }
                elseif(FncArray::ifReady($j))
                {
                    if(count($j) == 2)
                    {
                        $this->join[] = $j[0]. ' JOIN '. $j[1];
                    }
                    else
                    {
                        $this->join[] = implode(' ', $j);
                    }
                }
            }
        }
        elseif(is_string($joins))
        {
            $this->join[] = $joins;
        }
  
        return $this;
    }

    protected function buildSelect()
    { 
        if(empty($this->table)) Response::_404('Invalid table');
        if(empty($this->fields)) $this->fields[] = '*';

        $q = $this->countTotal ? 'SELECT COUNT(*) FROM '.$this->table : 'SELECT '. implode(',', $this->fields). ' FROM '.$this->table;

        if(count($this->join))
        {
            foreach($this->join as $join)
            $q .=  "\n ".$join;
        }

        if(count($this->where))
        {
            $q .= ' WHERE '. implode(' AND ', $this->where);
        }

        if(!empty($this->groupby))
        {
            $q .= ' GROUP BY '.$this->groupby;
        }

        if(!empty($this->orderby))
        {
            $q .= ' ORDER BY '.$this->orderby;
        }

        if(!empty($this->limit))
        {
            $q .= ' LIMIT '.$this->limit;
        }
        
        $this->query = $this->prefix($q);
    }

    public function row($conditions=array())
    {
        $this->where($conditions);
        $this->buildSelect();
        $data =  $this->db->fetch($this->query, $this->getValue());
        $this->reset();
        return $data;
    }

    public function list($start='0', $limit='20')
    {
        if(empty($start) && empty($limit))
        {
            $this->limit(''); 
        }
        else
        {
            $this->limit($start.', '.$limit);
        }

        $this->buildSelect();
        $data = $this->db->fetchAll($this->query, $this->getValue());

        if($this->countTotal)
        {
            $this->limit(''); // reset to count
            $this->buildSelect();
            $this->total = $this->db->fetchColumn($this->query, $this->getValue());
        }

        $this->reset();
        return $data;
    }

    public function insert($data=array())
    {
        $indexNum = false;
        if(is_array($data) || is_object($data))
        {
            foreach($data as $key=>$value)
            {
                if($key === 0)
                {
                    $indexNum = true;
                    break;
                }
                $this->select($key);
                $this->value($value, 'insert');
            }
        }

        if($indexNum)
        {
            $this->value($data, 'insert');
        }

        $value = array_fill(0, count($this->fields), '?');

        $q = 'INSERT INTO '. $this->table . '( '. implode(',', $this->fields ). ') VALUES ('. implode(',', $value).')';
 
        $this->sql = $this->prefix($q);

        $id = $this->db->insert($this->sql, $this->getValue());
        // Debug $q
        $this->reset();
        return $id;
    }

    public function insertBulk($data=array(), $fields=array())
    {
        if(!count($fields))
        {
            $fields = array_keys($data[0]);
        }

        $this->select($fields);
        $value = array_fill(0, count($fields), '?');
        $value = '(' . implode(',', $value).')';

        $values = [];
        foreach($data as $row)
        {
            $values[] = $value;
            $this->value($row, 'insert');
        }


        $q = 'INSERT INTO '. $this->table . '( '. implode(',', $this->fields ). ') VALUES '. implode(',', $values);
 
        $this->sql = $this->prefix($q);

        $try = $this->db->query($this->sql, $this->getValue());
        // Debug $q
        $this->reset();
        return $try;
    }

    public function update($data=array(), $conditions=array())
    {
        $updates = array();
 
        foreach( $data as $key=>$val)
        {
            $updates[] = $this->qq. $key. $this->qq. '= ?';
            $this->value($val, 'update');
        }

        $q = 'UPDATE '. $this->table . ' SET '. implode(',', $updates) ;

        $this->where($conditions);

        if(count($this->where))
        {
           
            $q .= ' WHERE '. implode(' AND ', $this->where);
        }

        $this->sql = $this->prefix($q);
 
        $res = $this->db->update($this->sql, $this->getValue());
        // Debug $q
        $this->reset();
        return $res;
    }

    public function delete($conditions=array())
    { 
        $q = 'DELETE FROM '. $this->table ;

        $this->where($conditions);

        if(count($this->where))
        {
           
            $q .= ' WHERE '. implode(' AND ', $this->where);
        }

        $this->sql = $this->prefix($q);
 
        $res = $this->db->delete($this->sql, $this->getValue());
        // Debug $q
        $this->reset();
        return $res;
    }

    public function exec($sql)
    { 
        $this->query = $this->prefix($sql);
        $res = $this->db->exec($this->query);
        // Debug $q
        $this->reset();
        return $res;
    }

    public function getLog()
    {
        return $this->db->getLog();
    }

    public function insertOnce($data, $conditions = [])
    {
        $table = $this->table;
        $id = 0;

        $try = count($conditions) ? $this->detail($conditions) : '';

        if( empty($try) )
        {
            $id = $this->table( $table )->insert($data);
        }

        // silent about the duplicate
        $this->reset();
        return $id;
    }

    public function detail(array $conditions, $select = null)
    {
        $try = $this->where($conditions);
        if( empty($this->fields) )
        {
            if( null === $select ) $select = array_keys($conditions);
            $try = $this->select($select);
        }
        
        return $try->row();
    }

    public function truncate($table = null)
    {
        if( null === $table )
        {
            if( empty($this->table) )
            {
                return false;
            }

            $table = $this->table;
        }

        return $this->exec( 'TRUNCATE TABLE '. $table );
    }

    // improve where
    // support OR / AND
    protected function subWhere( array $conditions, $key = false)
    {
        $ws = [];
        $vals = [];

        if( $key )
        {
            if(  in_array( strtoupper($key), ['OR', 'AND'] ) )
            {
                list($ws2, $vals2) = $this->subWhere( $conditions );
                $ws[] = ' ('. implode( ' '. $key. ' ', $ws2). ') ';
                $vals = array_merge($vals, $vals2);
            }
            else
            {
                $format = false;
                $value = $conditions[1];
                $operator = $conditions[0];

                if( false !== stripos($conditions[0], 'LIKE') )
                {
                    $format = '%__%';
                }

                if(isset($conditions[2]))
                {
                    $format = $conditions[2];
                } 
                
                if(is_array($value))
                {
                    $arr = [];
                    foreach($value as $val)
                    {
                        $arr[] = $this->qq($key). ' '. $operator. ' ?'; 
                        if( $format )
                        {
                            $value = str_replace('__', $val, $format);
                        }
                        $vals[] = $value;
                    }
                    $ws[] = implode( ' OR ', $arr);
                }
                else
                {
                    if('IN' == $operator && is_array($value))
                    {
                        $ws[] = $this->qq($key). ' IN ( ? )'; 
                        $vals[] = implode(',', $value);
                    }
                    else
                    {
                        if( $format )
                        {
                            $value = str_replace('__', $value, $format);
                        }
                        $ws[] = $this->qq($key). ' '. $operator. ' ?'; 
                        $vals[] = $value;
                    }
                }
            }
        }
        else
        {   
            foreach($conditions as $kk => $val)
            {
                if(is_array($val))
                {
                    list($w, $v) = is_numeric($kk) ?  $this->subWhere( $val ) : $this->subWhere( $val, $kk );
                    $ws = array_merge($ws, $w);
                    $vals = array_merge($vals, $v);
                }
                elseif(is_numeric($kk))
                {
                    $ws[] = $val;
                }
                else
                {
                    $ws[] = $this->qq($kk).' = ?';
                    $vals[] = $val;
                }
            }
        }

        return [ $ws, $vals ];
    }

    public function where($conditions)
    {
        if(FncArray::ifReady($conditions))
        {
            list($ws, $vals) = $this->subWhere($conditions); 
            foreach($ws as $wh)
            {
                //if(!is_string($wh)) die('Invalid condition ');
                $this->where($wh);
            }
            $this->value($vals);
        }
        elseif(is_string($conditions))
        {
            $this->where[] = $conditions;
        }
        
        return $this;
    }
    
    public function getStructure()
    {
        $q = "SHOW COLUMNS FROM ". $this->table;
        $this->query = $this->prefix($q);
        $res = $this->db->fetchAll($this->query);
        // Debug $q
        $this->reset();
        return $res;
    }
}
