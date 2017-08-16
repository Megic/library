<?php
/**
 * @link https://github.com/toriphes/yii2-console-runner
 * @package toriphes\console
 * @author Giulio Ganci <giulioganci@gmail.com>
 * @copyright Copyright (c) 2015 Giulio Ganci
 * @license BSD-3-Clause
 * @version 1.0
 */
namespace common\components;

use Yii;
use yii\base\Component;

/**
 * Class Runner - a component for running console command in yii2 web applications
 *
 * This extensions is inspired by the project https://github.com/vova07/yii2-console-runner-extension
 *
 * Basic usage:
 * ```php
 * use common\components\Runner;
 * $output = '';
 * $runner = new Runner();
 * $runner->run('controller/action param1 param2 ...', $output);
 * echo $output; //prints the command output
 * ```
 *
 * Application component usage:
 * ```php
 * //you config file
 * 'components' => [
 *     'consoleRunner' => [
 *         'class' => 'common\components\Runner'
 *     ]
 * ]
 * ```
 * ```php
 * //some application file
 * $output = '';
 * Yii::$app->consoleRunner->run('controller/action param1 param2 ...', $output);
 * echo $output; //prints the command output
 * ```
 * @author Giulio Ganci <giulioganci@gmail.com>
 */
class Runner extends Component
{
    /**
     * @var string yii console application file that will be executed
     */
    public $yiiscript;
    /**
     * @var string path to php executable
     */
    public $phpexec;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        set_time_limit(0);
        if ($this->yiiscript == null) {
            $this->yiiscript = "@app/yii";
        }
    }

    /**
     * Runs yii console command
     *
     * @param $cmd string command with arguments
     * @param string $output filled with the command output
     * @return int termination status of the process that was run
     */
    public function run($cmd, &$output = '')
    {
        $handler = popen($this->buildCommand($cmd), 'r');
        while (!feof($handler))
            $output .= fgets($handler);
        $output = trim($output);
        $status = pclose($handler);
        return $status;
    }

    /**
     * Builds the command string
     *
     * @param $cmd string Yii command
     * @return string full command to execute
     */
    protected function buildCommand($cmd)
    {
        return $this->getPHPExecutable() . ' ' . Yii::getAlias($this->yiiscript) . ' ' . $cmd . ' --no-color 2>&1';
    }

    /**
     * If property $phpexec is set it will be used as php executable
     *
     * @return string path to php executable
     */
    public function getPHPExecutable()
    {
        if ($this->phpexec) {
            return $this->phpexec;
        }
        return PHP_BINDIR . '/php';
    }
}