<?php
/**
 * SPT software - Plugin object
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A difinition for abstract a plugin object
 * @version: 0.8
 * 
 */

namespace SPT\Application\Plugin;

class Plugin
{
    private string $path;
    private string $namespace;
    private string $id;
    private ?array $details;
    private ?array $dependencies;

    public function __construct(string $id, string $path, string $namespace)
    {
        $this->id = $id;
        $this->path = $path;
        $this->namespace = $namespace;
        $this->details = null;
        $this->dependencies = null;
        $installer = $this->namespace. '\\registers\\Installer';
        if(!class_exists($installer))
        {
            throw new \Exception('Plugin '. $id. ' doesn\'t supply info.');
        }
    }

    public function getPath($subfolder = ''): string
    {
        return $this->path. $subfolder;
    }

    public function getNamespace($subclass = ''): string
    {
        return $this->namespace. $subclass;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDetails(): array
    {
        if(null === $this->details)
        {
            $installer = $this->namespace. '\\registers\\Installer';
            $this->details = method_exists($installer, 'info') ? $installer::info() : [];
        }

        return $this->details;
    }

    public function getDetail(string $key)
    {
        if(null === $this->details)
        {
            $this->getDetails();
        }
        return $this->details[$key] ?? '--';
    }

    public function getDependencies(): array
    {
        if(null === $this->dependencies)
        {
            $installer = $this->namespace. '\\registers\\Installer';
            $this->dependencies = method_exists($installer, 'dependencies') ? $installer::dependencies() : [];
        }

        return $this->dependencies;
    }

    public function getDependency(string $key): array
    {
        if(null === $this->dependencies)
        {
            $this->getDependencies();
        }
        return $this->dependencies[$key] ?? array();
    }
}