<?php
/**
 * SPT software - Core
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Just class for file
 * 
 */

namespace SPT;

use SPT\BaseObj;
use SPT\Traits\Log;
use SPT\Traits\ErrorString;

class File extends BaseObj
{
    use Log, ErrorString;

    protected $targetDir;
    protected $overwrite;
    protected $newName;
    protected $maxFileSize;
    protected $fileTypes;
    protected $fileMime;
    protected $cleanUpload;
    protected $cleanExtract;

    public function __construct()
    {
        // prepare a default properties;
        $this->setOptions([], true);
        $this->error = '';
    }

    public function setOptions(array $options, $reset = false)
    {
        $arr = [
            'targetDir' => SPT_STORAGE_PATH. 'upload',
            'overwrite' => false,
            'newName' => '',
            'maxFileSize' => '',
            'fileTypes' => '',
            'fileMime' => '',
            'cleanUpload' => false,
            'cleanExtract' => false,
        ];

        foreach($arr as $opt => $default)
        {
            if( $reset )
            {
                $this->{$opt} = isset($options[$opt]) ? $options[$opt] : $default;
            }
            elseif(isset($options[$opt]))
            {
                $this->{$opt} = $options[$opt];
            }
        }

        return $this;
    }

    public function check(array $file)
    {
        $newFileName = $this->newName ?  $this->newName : basename($file["name"]);
        $this->targetFile = $this->targetDir. '/'. $newFileName;

        // Check if file already exists
        if ( !$this->overwrite && file_exists($this->targetFile)) 
        {
            $this->error = $newFileName. ': File already exists.';
            return false;
        }

        // Check file size
        if ( $this->maxFileSize && $file["size"] > $this->maxFileSize) 
        {
            $this->error = $newFileName. ': File is too large.';
            return false;
        }

        // check file type, which is less meaning to MIME
        if( is_array($this->fileTypes) && !in_array($file['type'], $this->fileTypes) )
        {
            $this->error = $newFileName. ': Invalid file type.'. $file['type'];
            return false;
        }

        // check file MIME
        $mime = mime_content_type($file['tmp_name']);
        if( is_array($this->fileMime) && !in_array($mime , $this->fileMime) )
        {
            $this->error = $newFileName. ': Invalid file MIME.';
            return false;
        }
        
        /*
        TODO: check file error
        foreach ($_FILES["pictures"]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES["pictures"]["tmp_name"][$key];
                // basename() may prevent filesystem traversal attacks;
                // further validation/sanitation of the filename may be appropriate
                $name = basename($_FILES["pictures"]["name"][$key]);
                move_uploaded_file($tmp_name, "$uploads_dir/$name");
            }
        }*/

        return true;
    }

    public function upload(array $file)
    {
        if( ! $this->check($file) ) return false;
 
        if ( !move_uploaded_file($file["tmp_name"], $this->targetFile)) {
            $this->error = $newFileName. ': File can not upload, please check folder permission.';
            return false;
        }

        return true;
    }

    public function extract(array $file, string $extractDir = SPT_STORAGE_PATH. 'extract')
    {
        if( $this->cleanExtract && !$this->emptyFolder($extractDir) )
        {
            $this->error = 'Can\'t empty extract folder';
            return false;
        }

        if( !$this->setOptions([
            'fileTypes' => [
                'application/zip', 'application/octet-stream', 
                'application/x-rar-compressed', 'application/vnd.rar',
                'multipart/x-zip', 'application/x-zip-compressed',
                'application/x-tar'
            ]
        ])->upload($file) ) return false;

        $zip = new \ZipArchive; // TODO: consider PharData
        $res = $zip->open( $this->targetFile );
        if (true === $res) {
            $zip->extractTo($extractDir);
            $zip->close();
            return true;
        } else {
            $this->error = 'Can not open file zip';
            return false;
        }
    }

    public function removeFolder(string $dir)
    {
        if( !$this->emptyFolder($dir) ) return false;
        if( !rmdir($dir) )
        {
            $this->error = $dir.' can not be deleted.';
            return false;
        }

        return true;
    }

    public function emptyFolder(string $dir)
    {
        if( !is_dir($dir) )
        {
            $this->error = $dir.' is not a folder.';
            return false;
        }
 
        $objects = scandir($dir);
        foreach ($objects as $x) 
        { 
            if ($x != '.' && $x != '..') 
            { 
                if (is_dir($dir. '/' .$x) && !is_link($dir. '/'. $x))
                {
                    $this->emptyFolder($dir. '/' .$x);
                    rmdir($dir. '/' .$x);
                }
                else
                {
                    unlink($dir. '/' .$x);
                } 
            } 
        }
        return true;
    }

    public function copyFolder(string $dir, string $dest, $mode = 0755)
    {
        if( !is_dir($dir) )
        {
            $this->error = $dir.' is not a folder.';
            return false;
        }

        if( !is_dir($dest) && !mkdir($dest, $mode) )
        {
            $this->error = 'Can\'t create new folder '.$dest. '/'. $x;
            return false;
        }
 
        $objects = scandir($dir);
        foreach ($objects as $x) 
        { 
            if ($x != '.' && $x != '..') 
            { 
                if (is_dir($dir. '/'. $x) && !is_link($dir. '/'. $x))
                {
                    if ( !mkdir($dest. '/'. $x, $mode) )
                    {
                        $this->error = 'Can\'t create new folder '.$dest. '/'. $x;
                        return false;
                    }

                    $this->copyFolder($dir. '/'. $x, $dest. '/'. $x, $mode);
                }
                else
                {
                    if ( !copy($dir. '/' .$x, $dest. '/'. $x) )
                    {
                        $this->error = 'Can\'t copy file '. $dir. '/'. $x;
                        return false;
                    }
                } 
            } 
        }

        return true;
    }

    public function uploadImage(array $file)
    { 
        $imageFileType = strtolower(pathinfo($this->targetFile,PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image 
        $check = getimagesize($file["tmp_name"]);
        //Log::add  "File is an image - " . $check["mime"] . ".";
        if($check === false) 
        {
            $this->error = "File is not an image.";
            return false;
        }

        // Allow certain file formats
        if( $imageFileType != "jpg" 
         && $imageFileType != "png" 
         && $imageFileType != "jpeg"
         && $imageFileType != "gif" ) 
        {
            $this->error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            return false;
        }
        // TODO: support resize or stamp
    }
}
