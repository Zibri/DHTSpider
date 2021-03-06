<?php
/**
 * Desc: Log类
 * User: baagee
 * Date: 2019/2/26
 * Time: 下午6:32
 */

namespace DHT;

use Swoole\Process;

/**
 * Class Log
 * @package Dht\Console
 */
class Log
{
    /**
     * 允许的Log级别
     */
    private const ALLOW_LEVEL = [
        'info', 'notice', 'warning', 'error', 'metadata'
    ];

    /**
     * @param string $level
     * @param string $msg
     */
    public static function __callStatic(string $level, $msg)
    {
        $msg = date('Y-m-d H:i:s') . '  ' . $msg[0];
        if (in_array($level, self::ALLOW_LEVEL)) {
            self::console($level, $msg);
            // 记录
            $process = new Process(function (Process $worker) use ($msg, $level) {
                $log_file = implode(DIRECTORY_SEPARATOR, [getcwd(), 'log', date('Y_m_d'), date('H') . '_' . strtoupper($level) . '.log']);
                $log_path = dirname($log_file);
                if (!is_dir($log_path)) {
                    exec('mkdir -p ' . $log_path);
                }
                file_put_contents($log_file, $msg . PHP_EOL, FILE_APPEND | LOCK_EX);
                $worker->exit(0);
            }, false);
            $process->start();
        }
    }

    /**
     * @param string $level
     * @param string $msg
     */
    private static function console(string $level, string $msg)
    {
        $colorMsg = "\033[0;";// . $msg . PHP_EOL;
        $msg      = strtoupper($level) . ' ' . $msg;
        //"\033[0;30m Hello,world! \033[0m"
        switch ($level) {
            case 'info':
                // 绿色
                $colorMsg .= "32m";
                break;
            case "notice":
                // 蓝色
                $colorMsg .= "34m";
                break;
            case "warning":
                // 黄色
                $colorMsg .= "33m";
                break;
            case 'error':
                //红色
                $colorMsg .= "31m";
                break;
            default:
                //灰色
                $colorMsg .= "37m";
        }
        echo $colorMsg . $msg . "\033[0m" . PHP_EOL;
    }
}