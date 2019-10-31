<?php

namespace wdmg\terminal\controllers;

use Yii;
use yii\web\Controller;
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

        if ($phpversion = $this->runConsole('php', '-v'))
            $greetings = $this->module->name . ' [v.' . $this->module->version . '], ' . trim(preg_replace('/\n+/', '\n', $phpversion[1]));
        else
            $greetings = $this->module->name . ' [v.' . $this->module->version . '], PHP v.' . phpversion();

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
        // CLI is allowed?
        if (isset(Yii::$app->params['terminal.allowCLI']))
            $allowCLI = Yii::$app->params['terminal.allowCLI'];
        else
            $allowCLI = Yii::$app->controller->module->allowCLI;

        // Support CLI commands?
        if (isset(Yii::$app->params['terminal.supportCLI']))
            $supportCLI = Yii::$app->params['terminal.supportCLI'];
        else
            $supportCLI = Yii::$app->controller->module->supportCLI;

        $options = Json::decode(Yii::$app->request->getRawBody());
        if (intval($options['jsonrpc']) >= 2 && $options['method'] == "system.describe") {

            $params = explode(' ', $options['params'][0]);
            $cmd = $params[0];
            $command = str_replace($cmd.' ', '', $options['params'][0]);

            if ($cmd == 'actionCompress')
                return [];

            if ($cmd == 'yii') {
                list ($status, $output) = $this->runConsole(Yii::getAlias('@app/yii'), $command);
            } else {
                if ($allowCLI) {
                    if (in_array($cmd, $supportCLI)) {
                        list ($status, $output) = $this->runConsole($cmd, $command);
                    } else {
                        $output = '[[;orange;]Warning! The command `'.$cmd.'` not supported!]';
                    }
                } else {
                    $output = '[[;red;]Error! CLI command`s not allowed!]';
                }
            }


            return $this->asJson(['result' => $output]);
        }
        return [];
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
            $handler = popen($cmd . ' ' . $command . ' 2>&1', 'r');
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