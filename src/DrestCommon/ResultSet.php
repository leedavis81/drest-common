<?php
namespace DrestCommon;

/**
 * Drest result set
 * @author Lee
 */
class ResultSet implements \Countable, \IteratorAggregate, \ArrayAccess
{
    /**
     * Data - immutable and injected on construction
     * @var array $data
     */
    private $data;

    /**
     * Key name to be used to wrap the result set in
     * @var string $keyName
     */
    private $keyName;

    /**
     * Construct a result set instance
     * @param array $data
     * @param string $keyName
     * @throws \Exception
     */
    private function __construct(array $data, $keyName)
    {
        $keyName = preg_replace("/[^a-zA-Z0-9_\s]/", "", $keyName);
        if (!is_string($keyName)) {
            throw new \Exception('arent key name in a result set object is invalid. Must be an alphanumeric string (underscores allowed)');
        }
        $this->data = $data;
        $this->keyName = $keyName;
    }

    /**
     * Get the result set
     * @return array $result
     */
    public function toArray()
    {
        return array($this->keyName => $this->data);
    }

    /**
     * Create an instance of a results set object
     * @param array $data
     * @param string $keyName
     * @return ResultSet
     */
    public static function create($data, $keyName)
    {
        return new self($data, $keyName);
    }


    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if (isset($this->data[$offset])) {
            return $this->data[$offset];
        }
        return null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return bool
     */
    public function offsetSet($offset, $value)
    {
        if (!isset($offset)) {
            $this->data[] = $value;
            return true;
        }
        return $this->data[$offset] = $value;
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetUnset($offset)
    {
        if (isset($this->data[$offset])) {
            $removed = $this->data[$offset];
            unset($this->data[$offset]);
            return $removed;
        }
        return null;
    }
}