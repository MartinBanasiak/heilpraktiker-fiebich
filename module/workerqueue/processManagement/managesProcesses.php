<?php
declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 15.11.2016
 * Time: 19:02
 */

namespace DynCom\dc\workerqueue\processManagement;


/**
 * Class managesProcesses
 * @package DynCom\dc\workerqueue\processManagement
 */
trait managesProcesses
{

    /**
     * @param string $path
     * @return array
     */
    public function getPIDsForPath(string $path): array
    {
        //normalize slashes
        $path = str_replace(['/','\\'],DIRECTORY_SEPARATOR,$path);
        $out = [];
        if (stripos(PHP_OS,'WIN') === 0) {
            $cmd = 'powershell -Command " & Get-WmiObject Win32_Process | Where-Object {($_.CommandLine -like \'*' . escapeshellarg(basename($path)) . '*\') -and ($_.Path -like \'*php*.exe\')} | Select-Object ProcessId | Format-Table -HideTableHeaders';
        } else {
            $cmd = "ps aux | grep " . basename($path) . " | grep -v grep | awk '{print $2}'";
        }
        $output = [];
        $code = 0;
        exec($cmd,$output,$code);
        foreach ($output as $line) {
            if (!empty($line)) {
                $out[] = (int)trim($line);
            }
        }
        return (array)$out;
    }

    /**
     * @param int $pid
     * @return int
     */
    public function getProcessRuntimeSecondsByPID(int $pid) : int
    {
        if (stripos(PHP_OS,'WIN') === 0) {
            $cmd = 'powershell -Command " & (New-TimeSpan -ErrorAction SilentlyContinue -Start (Get-Process -Id ' . $pid .'.StartTime).TotalSeconds"';
        } else {
            $cmd = "echo $(($(date +%s) - $(stat -c %Z /proc/" . $pid . "))) | awk '{print int($1)}'";
        }
        $code = 0;
        $str = $this->execReturnString($cmd,$code);
        $seconds = (int)$str;
        return $seconds;
    }

    /**
     * @param int $pid
     * @return int
     */
    public function restartProcessByPID(int $pid): int
    {
        $newPID = null;
        $cmd = $this->getCommandLineExpressionForPID($pid);
        if ($cmd && $this->terminateProcessByPID($pid, SIGTERM)) {
            sleep(1);
            $newPID = $this->startProcessInBackground($cmd);
        }
        return (int)$newPID;
    }

    /**
     * @param int $pid
     * @return null|string
     */
    public function getCommandLineExpressionForPID(int $pid): ?string
    {
        if (0 === stripos(PHP_OS,'WIN')) {
            $cmd = 'powershell -Command " & Write-Host (Get-WmiObject Win32_Process -ErrorAction SilentlyContinue -Filter "ProcessId=' . $pid . '").CommandLine"';
        } else {
            $cmd = 'ps -ww -o args= -p ' . $pid;
        }
        $code = 0;
        $str = $this->execReturnString($cmd,$code);
        return empty($str) ? null : $str;
    }

    /**
     * @param int $pid
     * @param int $signal
     * @return bool
     */
    public function terminateProcessByPID(int $pid, int $signal): bool
    {
        return posix_kill($pid, $signal);
    }

    /**
     * @param string $cmd
     * @return int|null
     */
    public function startProcessInBackground(string $cmd): ?int
    {
        if (0 === stripos(PHP_OS,'WIN')) {
            //$cmd = $cmd . ' &';
            $cmd = 'powershell -Command " & Start-Process -NoNewWindow -ErrorAction SilentlyContinue ' . $cmd . ' 2>&1>$null"';
            //$cmd = 'powershell -Command " & Start-Job -ScriptBlock {' . $cmd . ' 2>&1} 2>&1|Out-Null"';
        } else {
            $cmd = '/usr/bin/nohup ' . $cmd . ' >/dev/null 2>&1 & echo $!';
        }

        $pid = (int)exec($cmd);
        return $pid > 0 ? $pid : null;
    }

    /**
     * @param string $cmd
     * @return bool
     */
    public function checkCommandExists(string $cmd) : bool
    {
        if (0 === stripos(PHP_OS,'WIN')) {
            $cmd = 'powershell if ((Get-Command  ' . escapeshellarg($cmd) . ' -ErrorAction SilentlyContinue) -eq $null) { $host.SetShouldExit(1); Write-Host 1; } else { $host.SetShouldExit(0); Write-Host 0; }';
        } else {
            $cmd = 'command -v ' . escapeshellarg($cmd) . ' >/dev/null 2>&1 || echo 1';
        }
        $result = (int)exec($cmd);
        return ($result !== 1);
    }

    public function execReturnArray(string $cmd, int &$returnCode) : array
    {
        $ret = [];
        exec($cmd,$ret,$returnCode);
        return $ret;
    }

    public function execReturnString(string $cmd, int &$returnCode) : string
    {
        $ret = '';
        $arr = [];
        exec($cmd,$arr,$returnCode);
        $ret = implode(PHP_EOL,$arr);
        return $ret;
    }

}