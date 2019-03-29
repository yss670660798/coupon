<?php

namespace App\Models;


class Menu
{
    public static function admin($resource = [])
    {
        $menu = config('adminMenu');
        $del = [];
        foreach ($menu as $k1 =>  $menu1) {
            if (isset($menu1['child'])) {
                foreach ($menu1['child'] as $k2 => $menu2) {
                    if (isset($menu2['child'])) {
                        foreach ($menu2['child'] as $k3 => $menu3) {
                            if (!in_array('get:' . $menu3['url'], $resource)) {
                                $del[] = $menu[$k1]['child'][$k2]['child'][$k3];
                                unset($menu[$k1]['child'][$k2]['child'][$k3]);
                            }
                        }
                        if (!$menu[$k1]['child'][$k2]['child']) {
                            $del[] = $menu[$k1]['child'][$k2];
                            unset($menu[$k1]['child'][$k2]);
                        }
                        continue;
                    }
                    if (!in_array('get:' . $menu2['url'], $resource)) {
                        $del[] = $menu[$k1]['child'][$k2];
                        unset($menu[$k1]['child'][$k2]);
                        continue;
                    }
                }
                if (!$menu[$k1]['child']) {
                    $del[] = $menu[$k1];
                    unset($menu[$k1]);
                }
                continue;
            }
            if (!in_array('get:' . $menu1['url'], $resource)) {
                $del[] = $menu[$k1];
                unset($menu[$k1]);
                continue;
            }
        }
        return $menu;
    }


    public static function resetParentUrl($menus)
    {
        foreach ($menus as &$menu)
        {
            if (!isset($menu['child'][0])) {
                continue;
            }
            $child = $menu['child'][0];
            if (!isset($child['child'])) {
                $menu['url'] = $child['url'];
                continue;
            }

            $menu['url'] = $child['child'][0]['url'];
        }
        return $menus;
    }
}
