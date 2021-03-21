<?php

namespace Live\Collection;

/**
 * Memory collection
 *
 * @package Live\Collection
 */
class MemoryCollection implements CollectionInterface
{
    /**
     * Collection data
     *
     * @var array
     */
    protected $data;

    /**
     * Collection ExpirationTime
     *
     * @var array
     */
    protected array $expirationTime;

    /**
     * Collection ExpirationTime
     *
     * @var int
     */
    protected int $defaultExpirationTime = 30;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->data                     = [];
        $this->expirationTime           = [];
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $index, $defaultValue = null)
    {
        if (!$this->has($index)) {
            return $defaultValue;
        }

        if (!$this->hasExpirationTime($index) || time() > $this->expirationTime[$index]) {
            return $defaultValue;
        }

        return $this->data[$index];
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $index, $value, $expirationTime = null)
    {
        $this->data[$index] = $value;
        if (is_null($expirationTime) || $expirationTime < 0) {
            $defaultExpirationTime      = $this->defaultExpirationTime;
            $expirationTime             =  time() + $defaultExpirationTime;
        } elseif ($expirationTime > 0) {
            $expirationTime             = time() + $expirationTime;
        }
        $this->expirationTime[$index]   = $expirationTime;
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $index)
    {
        return array_key_exists($index, $this->data);
    }

    /**
     * @param string $index
     * @return bool
     */
    public function hasExpirationTime(string $index)
    {
        if (array_key_exists($index, $this->expirationTime)) {
            if (time() <= $this->expirationTime[$index]) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * {@inheritDoc}
     */
    public function clean()
    {
        $this->data             = [];
        $this->expirationTime   = [];
    }
}
