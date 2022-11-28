<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v3;

use Yii;
use app\actions\api\v3\traits\ApiInitializerTrait;
use app\components\formatters\api\v3\BattleApiFormatter;
use app\components\helpers\UuidRegexp;
use app\models\Battle3;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

final class GetSingleBattleAction extends Action
{
    use ApiInitializerTrait;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->apiInit();
    }

    public function run(string $uuid, bool $full = false): Response
    {
        if (!\preg_match(UuidRegexp::get(true), $uuid)) {
            throw new BadRequestHttpException();
        }

        $model = Battle3::find()
            ->andWhere([
                'is_deleted' => false,
                'uuid' => $uuid,
            ])
            ->limit(1)
            ->one();

        if (!$model) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $user = $model->user;
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $isAuthenticated = Yii::$app->user->isGuest
            ? false
            : (int)$user->id === (int)Yii::$app->user->id;

        $resp = Yii::$app->response;
        $resp->data = BattleApiFormatter::toJson(
            $model,
            fullTranslate: $full,
            isAuthenticated: $isAuthenticated,
        );

        return $resp;
    }
}
