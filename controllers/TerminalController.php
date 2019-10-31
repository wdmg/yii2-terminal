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
     * Current path for CLI
     */
    private $path = null;

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


        $session = Yii::$app->session;
        $path = $session->get('terminal.path');

        // Set of current path
        if ($path)
            $this->path = $path;
        else
            $this->path = addslashes(Yii::getAlias('@app'));


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

        $promptRoute = Url::toRoute(['terminal/prompt']);
        $rpcRoute = Url::toRoute(['terminal/rpc']);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_terminal', [
                'module' => $this->module,
                'greetings' => $greetings,
                'promptRoute' => $promptRoute,
                'rpcRoute' => $rpcRoute,
                'prompt' => $this->getPrompt()
            ]);
        } else {
            return $this->render('index', [
                'module' => $this->module,
                'greetings' => $greetings,
                'promptRoute' => $promptRoute,
                'rpcRoute' => $rpcRoute,
                'prompt' => $this->getPrompt()
            ]);
        }
    }

    /**
     * Prompt action
     * @return array
     */
    public function actionPrompt()
    {
        return $this->asJson(['result' => $this->getPrompt()]);
    }

    /**
     * RPC action
     * @return array
     */
    public function actionRpc()
    {

        $session = Yii::$app->session;

        // Set of current path
        if (is_dir($this->path))
            chdir($this->path);

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

                        if ($cmd == 'cd') {
                            if (is_dir($command)) {
                                if (chdir($command) && getcwd()) {
                                    $this->path = getcwd();
                                    $session->set('terminal.path', $this->path);
                                }
                            }
                        }

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
            set_time_limit(30);
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

    /**
     * Get prompt
     * @return string
     */
    public function getPrompt()
    {
        $prompt = '$ ';

        if ($this->path)
            $prompt = $this->path . '$ ';

        if(!(Yii::$app->user->isGuest) && isset(Yii::$app->user->identity->username)) {
            $prompt = Yii::$app->request->serverName . ':~ ' . Yii::$app->user->identity->username . '$ ';

            if ($this->path)
                $prompt = Yii::$app->request->serverName . ':' . $this->path . ' ' . Yii::$app->user->identity->username . '$ ';

        }

        return $prompt;
    }
}