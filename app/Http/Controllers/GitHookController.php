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

        // Логирование неудачного ввода ключа
        if (!$secretKey || $secretKey != $requestKey) {
            HookLog::create([
                'ip_address' => $request->ip(),
                'action' => 'Invalid secret key',
            ]);
            return response()->json(['message' => 'Ошибка: неверный секретный ключ.'], 403);
        }

        // Логирование, если обновление уже выполняется
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

            // Логирование начала процесса обновления
            HookLog::create([
                'ip_address' => $ip,
                'action' => "Git update triggered"
            ]);

            $this->runGitCommands();

            // Логирование успешного завершения обновления
            HookLog::create([
                'ip_address' => $ip,
                'action' => 'Project updated successfully',
            ]);

            return response()->json(['message' => 'Project has been successfully updated.'], 200);
        } catch (\Exception $e) {
            // Логирование ошибки
            HookLog::create([
                'ip_address' => $request->ip(),
                'action' => 'Error during update'
            ]);

            Log::error('Error updating: ' . $e->getMessage());
            return response()->json(['message' => 'Error updating the project.'], 500);
        } finally {
            self::$isUpdating = false;
        }
    }

    private function runGitCommands()
    {
        $this->runCommand(['git', 'checkout', 'main'], 'Switching to the main branch');

        $this->runCommand(['git', 'reset', '--hard'], 'Canceling local changes');
        $this->runCommand(['git', 'pull', 'origin', 'main'], 'Git pull of the main brang');
    }

    private function runCommand(array $command, $logMessage)
    {
        // Логирование действия
        Log::info($logMessage);
        HookLog::create([
            'ip_address' => request()->ip(),
            'action' => $logMessage,
        ]);

        $process = new Process(
            $command,
            base_path()
        );

        $process->run();

        if (!$process->isSuccessful()) {
            // Логирование ошибки команды
            HookLog::create([
                'ip_address' => request()->ip(),
                'action' => 'Git command failed'
            ]);
            throw new \RuntimeException($process->getErrorOutput());
        }

        // Логирование успешного выполнения команды
        HookLog::create([
            'ip_address' => request()->ip(),
            'action' => 'Git command successful'
        ]);
        Log::info($process->getOutput());
    }
}
