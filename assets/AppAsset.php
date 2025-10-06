<?php
namespace app\assets;

use yii\web\AssetBundle;
use Yii;
use yii\helpers\Url;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [];
    public $js = [];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
        'yii\web\JqueryAsset',
    ];

    public function init()
    {
        parent::init();

        // Add CSS files
        $this->css = [
            'css/site.css',
            'css/style.css',
            'css/plugins/animate.min.css',
            'css/custom.css?ver=' . time(),
        ];

        // Determine if current action is login or register
        $route = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
        
        // Add JS files
        if (!in_array($route, ['site/login', 'registration/register'])) {
            // Load vendor-all.min.js only for login and register pages
            $this->js[] = 'js/vendor-all.min.js';
        }

        // These scripts are needed on all pages
        $this->js[] = 'js/pcoded.min.js';
        $this->js[] = 'js/custom.js?ver=' . time();

        // Example: You can log the route or assets if needed
        // Yii::debug("Current route: {$route}");
        // Yii::debug("JS files: " . json_encode($this->js));
    }
}
