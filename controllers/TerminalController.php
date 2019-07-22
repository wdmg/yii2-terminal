<?php

namespace wdmg\terminal\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * TerminalController
 *
 * @author Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @property wdmg\terminal\Module $module
 */
class TerminalController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public $defaultAction = 'index';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'roles' => ['admin'],
                        'allow' => true
                    ],
                ],
            ],
        ];

        // If auth manager not configured use default access control
        if(!Yii::$app->authManager) {
            $behaviors['access'] = [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'roles' => ['@'],
                        'allow' => true
                    ],
                ]
            ];
        }

        return $behaviors;
    }

    /**
     * Main index action.
     * @return mixed
     */
    public function actionIndex()
    {
        $greetings = $this->module->name . ' [v.' . $this->module->version . ']';

        $prompt = '$ ';
        $path = addslashes(Yii::getAlias('@app'));

        if ($path)
            $prompt = $path.'$ ';

        if(!(Yii::$app->user->isGuest) && isset(Yii::$app->user->identity->username)) {
            $prompt = Yii::$app->user->identity->username . ':' . Yii::$app->request->serverName . ' ~$ ';

            if ($path)
                $prompt = Yii::$app->user->identity->username . ':'. Yii::$app->request->serverName .' '.$path.'$ ';
        }

        return $this->render('index', [
            'module' => $this->module,
            'greetings' => $greetings,
            'prompt' => $prompt
        ]);
    }
}