<?php namespace Uit\Bmenu;


use Backend\Facades\BackendAuth;
use System\Classes\PluginBase;
use Event;
use BackendMenu;
use Backend;
use Uit\Bmenu\Models\Settings;

class Plugin extends PluginBase
{
    use \System\Traits\ViewMaker;

    public $user;

    public $assets = [
        'css' => [
            '/plugins/uit/bmenu/assets/css/bmenu.css'
        ],
        'js' => [
            '/plugins/uit/bmenu/assets/js/bmenu.js'
        ]
    ];

    public function registerSettings()
    {
        return [
            'location' => [
                'label'       => 'Backend menu in frontend',
                'description' => 'Toggle backend menu in frontend',
                'category'    => 'system::lang.system.categories.cms',
                'icon'        => 'icon-bars',
                'class'       => 'Uit\Bmenu\Models\Settings',
                'url'         => Backend::url('uit/bmenu/settings'),
                'order'       => 500,
            ]
        ];
    }

    public function boot()
    {

        if(Settings::get('show_menu')) {
            Event::listen('cms.page.postprocess', function ($controller, $url, $page, $dataHolder) {

                $this->loadBackendUser();
                if (!is_null($this->user)) {
                    $this->registerBackendNavigations();
                    $html = '';
                    $html .= $this->makeLayoutPartial(base_path('plugins/uit/bmenu/partials/menu'));
                    $html .= $this->makeAssets() . '</body>';
                    $dataHolder->content = str_replace('</body>', $html, $dataHolder->content);
                }

            });
        }

    }

    public function loadBackendUser()
    {
        $this->user = BackendAuth::getUser();
    }

    public function registerBackendNavigations()
    {
        BackendMenu::registerCallback(function ($manager) {
            $manager->registerMenuItems('October.Backend', [
                'dashboard' => [
                    'label' => 'backend::lang.dashboard.menu_label',
                    'icon' => 'icon-dashboard',
                    'iconSvg' => 'modules/backend/assets/images/dashboard-icon.svg',
                    'url' => Backend::url('backend'),
                    'permissions' => ['backend.access_dashboard'],
                    'order' => 10
                ],
                'cms' => [
                    'label' => 'cms::lang.cms.menu_label',
                    'icon' => 'icon-magic',
                    'iconSvg' => 'modules/cms/assets/images/cms-icon.svg',
                    'url' => Backend::url('cms'),
                    'order' => 100,
                    'permissions' => [
                        'cms.manage_content',
                        'cms.manage_assets',
                        'cms.manage_pages',
                        'cms.manage_layouts',
                        'cms.manage_partials'
                    ],
                ],
                'media' => [
                    'label' => 'backend::lang.media.menu_label',
                    'icon' => 'icon-folder',
                    'iconSvg' => 'modules/backend/assets/images/media-icon.svg',
                    'url' => Backend::url('backend/media'),
                    'permissions' => ['media.*'],
                    'order' => 200
                ], 'system' => [
                    'label' => 'system::lang.settings.menu_label',
                    'icon' => 'icon-cog',
                    'iconSvg' => 'modules/system/assets/images/cog-icon.svg',
                    'url' => Backend::url('system/settings'),
                    'permissions' => [],
                    'order' => 1000
                ]
            ]);
        });

    }

    public function makeAssets()
    {
        $assetsHtml = '';
        foreach ($this->assets['js'] as $js) {
            $assetsHtml .= '<script src="' . url($js) . '"></script>';
        }
        foreach ($this->assets['css'] as $css) {
            $assetsHtml .= '<link rel="stylesheet" href="' . url($css) . '"/>';
        }
        return $assetsHtml;
    }
}
