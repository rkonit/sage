<?php

namespace App\AssetPipeline;

//use SplFileInfo;

class Asset
{
    public string $path;
    public string $logicalPath;

    public function __construct(string $path, string $logicalPath)
    {
        $this->path = $path;
        $this->logicalPath = $logicalPath;
    }

    public function content()
    {
        return file_get_contents($this->path);
    }

    // TODO: mime_content_type is inaccurate
    public function contentType()
    {
        //return mime_content_type($this->path);
        return "application/javascript";
    }

    public function isFresh(string $digest)
    {
        return $this->digest() === $digest || $this->alreadyDigested();
    }

    public function length()
    {
        return filesize($this->path);
    }

    public function digest()
    {
        return sha1_file($this->path);
    }

    public function digestedPath()
    {
        if ($this->alreadyDigested()) {
            return $this->logicalPath;
        } else {
            return preg_replace("/\.(\w+)$/", "-" . $this->digest() . ".$1", $this->logicalPath);
        }
    }

    private function alreadyDigested()
    {
        return preg_match("/-([0-9a-f]{7,128})\.digested\.[^.]+\z/", $this->logicalPath);
    }
}