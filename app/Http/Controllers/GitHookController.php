<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Process\Process;
use App\Models\HookLog;

class GitHookController extends Controller
{
    protected static $isUpdating = false;

    public function validateSecretKey(Request $request)
    {
        $secretKey = env('SECRET_KEY');
        $requestKey = $request->input('secret_key');

        if (!$secretKey || $secretKey != $requestKey) {
            HookLog::create([
                'ip_address' => $request->ip(),
                'action' => 'Invalid secret key',
            ]);
            return response()->json(['message' => 'Ошибка: неверный секретный ключ.'], 403);
        }

        if (self::$isUpdating) {
            HookLog::create([
                'ip_address' => $request->ip(),
                'action' => 'Update in progress, try again later',
            ]);
            return response()->json(['message' => 'Обновление уже выполняется, подождите завершения.'], 429);
        }

        self::$isUpdating = true;

        try {
            $ip = $request->ip();

            HookLog::create([
                'ip_address' => $ip,
                'action' => "Git update triggered"
            ]);

            $this->runGitCommands();

            HookLog::create([
                'ip_address' => $ip,
                'action' => 'Project updated successfully',
            ]);

            return response()->json(['message' => 'Проект успешно обновлен!'], 200);
        } catch (\Exception $e) {

            HookLog::create([
                'ip_address' => $request->ip(),
                'action' => 'Ошибка обновления'
            ]);

            Log::error('Error updating: ' . $e->getMessage());
            return response()->json(['message' => 'Возникла ошибка в результате обновления...'], 500);
        } finally {
            self::$isUpdating = false;
        }
    }

    private function runGitCommands()
    {
        $sshKeyPath = "C:\Users\Ameli\.ssh\id_ed25519"; // изменить
        $gitPath = "C:\Program Files\Git\bin\git.exe";
        $this->runCommand(['git', 'checkout', 'main'], 'Switching to the main branch');

        $this->runCommand(['git', 'reset', '--hard'], 'Canceling local changes');

        $this->runCommand([
            $gitPath,
            '-c',
            'core.sshCommand=ssh -i ' . $sshKeyPath . ' -o StrictHostKeyChecking=no',
            'pull',
            'git@github.com:backto0hell/labrary2.git',
            'main'
        ], 'Git pull of the main brang');
    }

    private function runCommand(array $command, $logMessage)
    {
        Log::info($logMessage);
        HookLog::create([
            'ip_address' => request()->ip(),
            'action' => $logMessage,
        ]);
        $repPath = "C:\VS-Server\labrary2"; // изменить
        $process = new Process(
            $command,
            $repPath,
        );

        $process->run();

        if (!$process->isSuccessful()) {

            HookLog::create([
                'ip_address' => request()->ip(),
                'action' => 'Git command failed'
            ]);
            throw new \RuntimeException(
                "Command failed: " . implode(' ', $command) . "\nError: " . $process->getErrorOutput()
            );
        }

        HookLog::create([
            'ip_address' => request()->ip(),
            'action' => 'Git command successful'
        ]);
        Log::info($process->getOutput());
    }
}
