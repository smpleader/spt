<?php
/**
 * SPT software - A Plugin for CMS
 * 
 * @project: https://github.com/smpleader/spt-boilerplate
 * @author: Pham Minh - smpleader
 * @description: Just a CMS Plugin
 * 
 */

namespace SPT\Plugin;

class CMS extends Base
{
    public function getInfo()
    {
        return [
            'title' => 'Plugin CMS',
            'name' => 'plgCMS',
            'version' => '0.0.2',
            'schema_version' => '0.0.2'
        ];
    }

    public function registerRouter()
    {
        return [];
    }

    // hook in manage
    public function install(){

        $plugin = $this->getInfo();
        $slug = $this->app->config->exists('sitepath') ? $this->app->config->sitepath : '';
        $arr = $this->registerRouter();
        $arr = $this->app->router->flatNodes($arr, $slug);
        $endpoints = [];
        if(count($arr))
        {
            foreach($arr as $key => $value)
            {
                $value['slug'] = $key;
                $value['plugin'] = $plugin['name'];
                $data = $this->app->SitemapEntity->endpointsFromArray($value);
            }
    
            foreach ($endpoints as $endpoint)
            {
                $this->app->SitemapEntity->add($endpoint);
            }
        }

        $newData = $plugin;
        $newData['settings'] = json_encode($this->getSettings());
        $this->app->PluginEntity->add( $newData );
    }

    public function upgrade()
    {
        $plugin = $this->getInfo();
        $record = $this->app->PluginEntity->findOne(['plugin' => $plugin['name']]);
        if ($plugin['version'] != $record['version'])
        {
            $record['version'] = $plugin['version'];
            $this->app->PluginEntity->update($record);
        }

        return true;
    }

    public function uninstall()
    {
        $plugin = $this->getInfo();
        // records in table sitemap
        $list = $this->app->SitemapEntity->list(0, 0, ['plugin' => $plugin['name']]);
        foreach ($list as $item)
        {
            $this->app->SitemapEntity->remove($item['id']);
        }

        // records in table plugin 
        $plugin = $this->app->PluginEntity->findOne(['plugin' => $plugin['name']]);
        $this->app->PluginEntity->remove($plugin['id']);
        return true;
    }
}