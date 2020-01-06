<?php


namespace App\Repository;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * @package App\Repository
 */
final class NoSqlManager implements DbManager
{

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $index;

    /**
     * @param string $basePath
     * @param string $table
     * @param string $index
     */
    public function __construct(string $basePath, string $table, string $index = 'id')
    {
        $this->table = $table;
        $this->basePath = $basePath;
        $this->index = $index;
    }

    /**
     * Return the name of the file where will be saved/read the element.
     *
     * @param array $element
     *
     * @return string
     */
    private function fileName(array $element): string
    {

        if (!key_exists($this->index, $element)) {
            throw new \RuntimeException(sprintf('This element doesn\'t have the id %s', $this->index));
        }

        return $this->table . '_' . $element[$this->index];
    }

    /**
     * @param mixed $id
     * @return string
     */
    private function fileNameById($id): string
    {
        if (empty($id)) {
            throw new \RuntimeException('The index cannot be empty');
        }

        return $this->table . '_' . $id;
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    private function path(string $fileName)
    {
        return $this->basePath . '/' . $fileName . '.json';
    }

    /**
     * @param array $element
     */
    private function write(array $element)
    {
        file_put_contents($this->path($this->fileName($element)), json_encode($element));
    }

    /**
     * @param mixed $id
     *
     * @return array
     */
    private function read($id)
    {
        $path = $this->path($this->fileNameById($id));

        if (!file_exists($path)) {
            throw new ResourceNotFoundException('no table found');
        }

        $content = file_get_contents($path);
        return json_decode($content, true);
    }

    /**
     * @param array $entity
     */
    public function save(array $entity)
    {
        $this->write($entity);
    }

    /**
     * @param mixed $id
     *
     * @return array
     */
    public function getById($id)
    {
        return $this->read($id);
    }
}