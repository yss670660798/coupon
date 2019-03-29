<?php
/**
 * Created by PhpStorm.
 * User: shkjadmin
 * Date: 2018/6/26
 * Time: 17:56
 */

return [
    'home'    => [
        'name' => '首页',
        'url'  => '',
        'icon' => '',
    ],
    'model'    => [
        'name'  => '模型中心',
        'url'   => '',
        'icon'  => '',
        'child' => [
            'model_list'  => [
                'name' => '模型列表',
                'url'  => '/admin/model',
                'icon' => '',
            ],
            'model_train'  => [
                'name' => '训练模型',
                'url'  => '/admin/model/train',
                'icon' => '',
            ],
            'model_check'  => [
                'name' => '模型校验',
                'url'  => '/admin/model/check',
                'icon' => '',
            ],
        ],
    ],
    'data'  => [
        'name'  => '数据中心',
        'url'   => '',
        'icon'  => 'cog',
        'child' => [
            'data_store'    => [
                'name'  => '门店管理',
                'url'   => '/brand/data/store',
                'icon'  => '',
            ],
            'data_sku'    => [
                'name'  => '产品管理',
                'url'   => '/brand/data/center/sku',
                'icon'  => '',
            ],
            'data_source'=>[
                'name'  => '数据集管理',
                'url'   => '/brand/data/center/source',
                'icon'  => '',
            ],
        ],
    ],
];