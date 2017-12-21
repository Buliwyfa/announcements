<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\announcements\assets;

use yii\web\AssetBundle;

class AnnouncementsAsset extends AssetBundle
{

    public $jsOptions = ['position' => \yii\web\View::POS_END];
    public $sourcePath = '@announcements/resources';
    public $css = [];
    public $js = [
        'js/humhub.announcements.js'
    ];
}
