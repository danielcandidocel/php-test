<?php

namespace Live\Collection;

/**
 * Class FileCollection
 * @package Live\Collection
 */
class FileCollection implements CollectionInterface
{
    /**
     * @const FILENAME
     */
    const FILENAME = 'files/file.txt';
    /**
     * @var false|resource
     */
    protected $file;
    /**
     * Collection data
     *
     * @var array
     */
    protected array $data;

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
        $this->data             = [];
        $this->expirationTime   = [];
        $this->file             = fopen(self::FILENAME, 'w+');
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $index, $defaultValue = null)
    {
        if (!$this->has($index)) {
            return $defaultValue;
        }

        if (!$this->hasExpirationTime($index)) {
            return $defaultValue;
        }

        return $this->data[$index];
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $index, $value, $expirationTime = null)
    {
        if (is_null($expirationTime) || $expirationTime < 0) {
            $defaultExpirationTime  = $this->defaultExpirationTime;
            $expirationTime         = time() + $defaultExpirationTime;
        } elseif ($expirationTime > 0) {
            $expirationTime         = time() + $expirationTime;
        }

        $data = '';
        if (is_array($value)) {
            foreach ($value as $v) {
                $data .= $v.'#';
            }
        } else {
            $data   = $value;
        }

        $content    = $index.','.$data.','.$expirationTime.PHP_EOL;

        return fwrite($this->file, $content);
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $index): bool
    {
        $array          = $this->readFile();
        $columnIndex    = array_column($array, 'index');

        if (!in_array($index, $columnIndex)) {
            return false;
        } else {
            $key                = array_search($index, $columnIndex);
            $this->data[$index] = $array[$key]['value'];
            return true;
        }
    }

    /**
     * @param string $index
     * @return bool
     */
    public function hasExpirationTime(string $index): bool
    {
        $array          = $this->readFile();
        $columnIndex    = array_column($array, 'index');

        if (!in_array($index, $columnIndex)) {
            return false;
        } else {
            $key                            = array_search($index, $columnIndex);
            $this->expirationTime[$index]   = $array[$key]['expirationTime'];

            if (time() <= $this->expirationTime[$index]) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function readFile(): array
    {
        $array = [];

        if (file_exists(self::FILENAME)) {
            $contentFile = file(self::FILENAME);
            for ($x = 0; $x < count($contentFile); $x++) {
                $line = explode(',', $contentFile[$x]);
                if (strpos($line[1], '#')) {
                    $contentLine = explode('#', $line[1]);
                } else {
                    $contentLine = $line[1];
                }
                $arr = [
                    'index'             => $line[0],
                    'value'             => $contentLine,
                    'expirationTime'    => $line[2],
                ];
                array_push($array, $arr);
            }
        }

        return $array;
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        $this->data = $this->readFile();

        return count($this->data);
    }

    /**
     * {@inheritDoc}
     */
    public function clean()
    {
        new FileCollection();
    }
}
