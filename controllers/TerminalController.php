<?php

namespace wdmg\terminal\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Url;
use yii\helpers\Json;
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
     * @inheritdoc
     */
    public function init()
    {
        Yii::$app->request->enableCsrfValidation = false;
        parent::init();
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

        $rpcRoute = Url::toRoute(['terminal/rpc']);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_terminal', [
                'module' => $this->module,
                'greetings' => $greetings,
                'rpcRoute' => $rpcRoute,
                'prompt' => $prompt
            ]);
        } else {
            return $this->render('index', [
                'module' => $this->module,
                'greetings' => $greetings,
                'rpcRoute' => $rpcRoute,
                'prompt' => $prompt
            ]);
        }
    }


    /**
     * RPC action
     * @return array
     */
    public function actionRpc()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $options = Json::decode(Yii::$app->request->getRawBody());



        if (intval($options['jsonrpc']) >= 2 && $options['method'] == "system.describe") {
            list ($status, $output) = $this->runConsole(Yii::getAlias('@app/yii'), implode(' ', $options['params']));
            return ['result' => $output];
        }
    }


    /**
     * Runs console command
     *
     * @param string $cmd
     * @param string $command
     * @return null or array [status, output]
     */
    private function runConsole($cmd, $command)
    {
        if ($cmd) {
            set_time_limit(0);
            $cmd = Yii::getAlias($cmd) . ' ' . $command . ' 2>&1';
            $handler = popen($cmd, 'r');
            $output = '';
            while (!feof($handler)) {
                $output .= fgets($handler);
            }
            return [pclose($handler), trim($output)];
        } else {
            return null;
        }
    }
}