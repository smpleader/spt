<?php
/**
 * SPT software - Upload File
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Object equal to $_FILE PHP, support for clas ParseInputStream
 *              public properties because of non setter, getter in PHP
 * 
 */


namespace SPT\Support;

class UploadedFile
{
    /**
     * Internal variable for a file name
     * @var string $name
     */
    public $name;

    /**
     * Internal variable for a file path
     * @var string $path
     */
    public $path;

    /**
     * Internal variable for a  file size
     * @var int $size
     */
    public $size;

    /**
     * Internal variable for a file type
     * @var string $type
     */
    public $type;

    /**
     * A constructor
     * 
     * @return void
     */ 
    public function __construct($name, $path, $size, $type)
    {
        $this->name = $name;
        $this->path = $path;
        $this->size = $size;
        $this->type = $type;
    }
}