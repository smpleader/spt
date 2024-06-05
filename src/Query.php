<?php
/**
 * SPT software - A Query builder class
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A query builder wrap SPT\Extend\Pdo for simpler and more advanced usage
 * 
 */

namespace SPT;

use SPT\Support\FncArray as Arr;
use SPT\Extend\Pdo as PDOWrapper;

class Query
{
    /**
     * Internal database connection
     * @var PDOWrapper $db
     */
    protected $db;

    /**
     * Internal string query to get list, it's kept to re-use in many functions at one process flow
     * @var string $query
     */
    protected $query;

    /**
     * Internal string query for CRUD
     * @var string $sql
     */
    protected $sql;

    /**
     * Internal array to keep database prefix ( support many prefixes )
     * @var array $prefix
     */
    protected array $prefix;

    /**
     * Internal string to keep database quotation 
     * @var string $qq
     */
    protected $qq;

    /**
     * Internal string to keep table name in current query, mostly use for main table
     * @var string $table
     */
    protected $table;

    /**
     * Internal array to keep table's fields in current query
     * @var array $fields
     */
    protected $fields;

    /**
     * Internal array to keep query joins senetence
     * @var array $join
     */
    protected $join;

    /**
     * Internal array to keep query where conditions
     * @var array $where
     */
    protected $where;

    /**
     * Internal array to keep query value
     * @var array $value
     */
    protected $value;

    /**
     * Internal string to keep order by information
     * @var string $orderby
     */
    protected $orderby;

    /**
     * Internal string to keep group by information
     * @var string $groupby
     */
    protected $groupby;

    /**
     * Internal string to keep record limit information, could be include limits and offset
     * @var string $limit
     */
    protected $limit;

    /**
     * Set if next query need to get the total of current query or not.
     * @var bool $countTotal
     */
    protected bool $countTotal;

    /**
     * Cache the total of records with current query
     * @var int $total
     */
    protected int $total;

    /**
     * Require minium 2 dependencies
     *
     * @param PDOWrapper $db  SPT\Extend\Pdo object to work with database
     * @param array     $prefix Table prefix(es) array
     * @param string     $fquota Database quatation information
     * 
     * @return void
     */ 
    public function __construct(PDOWrapper $db, array $prefix, $fquota='`')
    {
        $this->db = $db;
        $this->prefix = $prefix;
        $this->qq = $fquota;
        $this->total = 0;
        $this->reset();
    }

    /**
     * Set default empty value for properties used for query
     * 
     * @return void
     */ 
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
    }

    /**
     * Fill prefix for a query string
     *
     * @param string  $query Query string
     * 
     * @return string $query Query string after filled prefix
     */ 
    protected function prefix(string $query)
    {
        if(Arr::isReady($this->prefix))
        {
            foreach($this->prefix as $find=>$replace)
            {
                $query = str_replace($find, $replace, $query);
            }
        }
        return $query;
    }

    /**
     * Fill quotation for a table or field 
     *
     * @param string  $name Table or field string
     * 
     * @return string $name Table or field with quotation
     */ 
    protected function qq(string $name)
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

    /**
     * Check if connection is ready or not
     * 
     * @return bool  Alias to object database's connection status
     */ 
    public function isConnected()
    {
        return $this->db->connected;
    }

    /**
     * Get total records of current query
     * 
     * @return int $total
     */ 
    public function total()
    {
        $total = $this->total;
        $this->total = 0;
        return $total;
    }

    /**
     * Remark if we need to get total of recrod with current query
     *
     * @param bool  $boolean true to count
     * 
     * @return Query $this
     */ 
    public function countTotal(bool $boolean = true)
    {
        $this->countTotal = $boolean;
        return $this;
    }

    /**
     * Set table name
     *
     * @param string  $name
     * 
     * @return Query $this
     */ 
    public function table(string $name)
    {
        $this->table = $this->qq($this->prefix($name));
        return $this;
    }

    /**
     * Set selected fields
     *
     * @param string|array  $fields
     * 
     * @return Query $this
     */ 
    public function select($fields)
    {
        if(Arr::isReady($fields))
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

    /**
     * Set value and its place
     *
     * @param mixed  $value
     * @param string  $place  because of query types, need to differ if the value for condition or value, 3 types: udpate, insert, where
     * 
     * @return Query $this
     */ 
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

    /**
     * Get value order by its place
     * Value for "update" or "insert" should come first.
     * The condition "where" is always in the last.
     * 
     * @return array $arr
     */ 
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

    /**
     * Set orderby
     *
     * @param mixed  $order
     *  
     * @return Query $this
     */ 
    public function orderby($order){

        if(Arr::isReady($order))
        {
            $this->orderby = implode(' ', $order);
        }
        elseif(is_string($order))
        {
            $this->orderby = $order;
        }
  
        return $this;
    }

    /**
     * Set groupby
     *
     * @param mixed  $group
     *  
     * @return Query $this
     */ 
    public function groupby($group){

        if(Arr::isReady($group))
        {
            $this->groupby = implode(', ', $group);
        }
        elseif(is_string($group))
        {
            $this->groupby = $group;
        }
  
        return $this;
    }

    /**
     * Set limit & offset
     *
     * @param mixed  $limit
     *  
     * @return Query $this
     */ 
    public function limit($limit){

        if(Arr::isReady($limit))
        {
            $this->limit = implode(', ', $limit);
        }
        elseif(is_string($limit) || is_numeric($limit))
        {
            $this->limit = $limit;
        }
  
        return $this;
    }

    /**
     * Set join condition
     *
     * @param mixed  $joins
     *  
     * @return Query $this
     */ 
    public function join($joins){

        if(Arr::isReady($joins))
        {
            foreach($joins as $j)
            {
                if(is_string($j))
                {
                    $this->join[] = $j;
                }
                elseif(Arr::isReady($j))
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

    /**
     * Build query Select
     *
     * @param bool  $buildForCount check if want to return query to count the total
     *  
     * @return Query $this
     */ 
    protected function buildSelect(bool $buildForCount = false)
    { 
        if(empty($this->table)) Response::_404('Invalid table');
        if(empty($this->fields)) $this->fields[] = '*';

        $q = $buildForCount ? 'SELECT COUNT(*) FROM '.$this->table : 'SELECT '. implode(',', $this->fields). ' FROM '.$this->table;

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

    /**
     * Get a row in array format
     *
     * @param mixed  $conditions Value for where conition
     *  
     * @return array $data
     */ 
    public function row($conditions=array())
    {
        $this->where($conditions);
        $this->buildSelect();
        $data =  $this->db->fetch($this->query, $this->getValue());
        $this->reset();
        return $data;
    }

    /**
     * Get an array list
     *
     * @param string|integer  $start Offset index to start query
     * @param string|integer  $limit Total recrod to request
     *  
     * @return array $data
     */ 
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
            $this->buildSelect(true);
            $this->total = (int) $this->db->fetchColumn($this->query, $this->getValue());
        }

        $this->reset();
        return $data;
    }

    /**
     * Insert a new row
     *
     * @param array|object  $data Insert object data or an array into a table
     *  
     * @return int $id
     */ 
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

    /**
     * Insert array of array data
     *
     * @param array  $data An array of data
     * @param array  $fields An array of columns
     *  
     * @return bool $try Return true if the query run successfully
     */ 
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

    /**
     * Update a row
     *
     * @param array|object  $data An array or object
     * @param array  $conditions Where for a condition
     *  
     * @return bool $res Return true if the query run successfully
     */ 
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

    /**
     * Remove a row
     *
     * @param array  $conditions Where for a condition
     *  
     * @return bool $res Return true if the query run successfully
     */ 
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

    /**
     * A general command to execute a query string
     *
     * @param string  $sql Query string
     *  
     * @return bool $res Return true if the query run successfully
     */ 
    public function exec(string $sql)
    { 
        $this->query = $this->prefix($sql);
        $res = $this->db->exec($this->query);
        // Debug $q
        $this->reset();
        return $res;
    }

    /**
     * A way to get log from PDO
     *  
     * @return array Alias to database log
     */ 
    public function getLog()
    {
        return $this->db->getLog();
    }

    /**
     * A way to insert record once
     * 0 means there is existing one 
     *
     * @param array|object  $data 
     * @param array|string  $conditions 
     *  
     * @return int id
     */ 
    public function insertOnce($data, $conditions = [])
    {
        $table = $this->table;
        $id = 0;

        $try = count($conditions) ? $this->detail($conditions) : '';

        if( empty($try) )
        {
            $id = $this->table( $table )->insert($data);
        }

        // silent if there is an existence 
        $this->reset();
        return $id;
    }

    /**
     * Get a row, run like this
     *  $this->table($name)->detail($where)
     *  or
     *  $this->table($name)->select('*')->detail($where)
     *
     * @param array|object  $conditions 
     * @param array|string  $select 
     *  
     * @return array row
     */ 
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

    /**
     * Empty a table
     *
     * @param string  $table 
     *  
     * @return bool 
     */ 
    public function truncate(string $table = null)
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

    /**
     * Get columns belongs to a table
     * Available with Mysql, PostgreSQl
     *
     * @param string  $table 
     *  
     * @return array 
     */ 
    public function structureTable($table = null)
    {
        if( null === $table )
        {
            if( empty($this->table) )
            {
                return false;
            }

            $table = $this->table;
        }
        
        return $this->db->fetchAll( 'SHOW COLUMNS FROM '. $this->prefix($table) );
    }

    /**
     * Edit a table structure
     *
     * @param mixed  $fields 
     * @param string  $table 
     *  
     * @return bool 
     */ 
    public function alterTable( $fields, $table = null)
    {
        if( null === $table )
        {
            if( empty($this->table) )
            {
                return false;
            }

            $table = $this->table;
        }

        if( is_array($fields) )
        {
            $fields = implode( ",\n", $fields);
        }

        return $this->exec( 'ALTER TABLE '. $table. " \n". $fields );
    }

    /**
     * Edit a table structure
     *
     * @param mixed  $fields 
     * @param string  $table 
     *  
     * @return bool 
     */ 
    public function createTable( $fields, $table = null)
    {
        if( null === $table )
        {
            if( empty($this->table) )
            {
                return false;
            }

            $table = $this->table;
        }

        if( is_array($fields) )
        {
            $fields = implode( ",\n", $fields);
        }

        return $this->exec( 'CREATE TABLE '. $table. " \n(". $fields. "\n)" );
    }

    /**
     * An internal function to support function "where" to deal with OR / AND
     *
     * @param array  $conditions 
     * @param bool|string  $key 
     *  
     * @return array 
     */ 
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

    /**
     * Set conditions for query
     *
     * @param array|string  $conditions 
     *  
     * @return Query $this 
     */ 
    public function where($conditions)
    {
        if(Arr::isReady($conditions))
        {
            list($ws, $vals) = $this->subWhere($conditions); 
            foreach($ws as $wh)
            {
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

    /**
     * Get total of record in the table
     *
     * @param string  $ct 
     *  
     * @return number  
     */ 

    public function countTotalRow($ct='*')
    {
        return $this->db->fetchColumn('SELECT COUNT('. $ct. ') FROM '. $this->prefix($this->table));
    }
}
