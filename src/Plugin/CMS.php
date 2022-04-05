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
        $arr = $this->registerRouter();
        $arr = $this->app->router->flatNodes($arr);
        $endpoints = [];
        if(count($arr))
        {
            foreach($arr as $key => $value)
            {
                $value['slug'] = $key;
                $value['plugin'] = $plugin['name'];
                $data = $this->app->SitemapEntity->endpointsFromArray($value);
                $endpoints = array_merge($data, $endpoints);
            }
    
            foreach ($endpoints as $endpoint)
            {
                $this->app->SitemapEntity->add($endpoint);
            }
        }

        $newData = $plugin;
        $newData['settings'] = '{}'; 
        $newData['active'] = 1; 
        $this->app->PluginEntity->add( $newData );
    }

    public function upgrade()
    {
        $plugin = $this->getInfo();
        $record = $this->app->PluginEntity->findOne(['name' => $plugin['name']]);
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
        $plugin = $this->app->PluginEntity->findOne(['name' => $plugin['name']]);
        $this->app->PluginEntity->remove($plugin['id']);
        return true;
    }
}