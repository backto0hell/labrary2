<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HookLog;
use Illuminate\Process\Exceptions\ProcessFailedException;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Cache;

class GitHookController extends Controller
{
    public function validateSecretKey(Request $request)
    {
        $secretKey = env('SECRET_KEY');
        $inputKey = $request->input('secret_key');

        // Проверка на наличие секретного ключа
        if (!$inputKey) {
            return response()->json(['error' => "Параметр 'secret_key' отсутствует"], 400);
        }

        // Проверка на совпадение секретного ключа
        if ($inputKey === $secretKey) {
            $ipAddress = $request->ip();
            $currentDate = now();

            // Логирование начала работы хука
            HookLog::create([
                'ip_address' => $ipAddress,
                'action' => 'Git hook triggered',
                'details' => json_encode(['date' => $currentDate]),
            ]);

            // Проверка блокировки, чтобы предотвратить одновременное выполнение
            if (Cache::has('git_update_lock')) {
                return response()->json(['message' => 'Обновление уже в процессе. Попробуйте позже.'], 409);
            }

            // Устанавливаем блокировку на 5 минут
            Cache::put('git_update_lock', true, now()->addMinutes(5));

            try {
                // Выполнение команд Git
                $output = $this->executeGitCommands();

                // Логирование успешного завершения операции
                HookLog::create([
                    'ip_address' => $ipAddress,
                    'action' => 'Git update completed',
                    'details' => json_encode(['output' => $output]),
                ]);

                return response()->json(['message' => 'Операция успешно выполнена', 'details' => $output], 200);
            } catch (\Exception $e) {
                // Логирование ошибки при выполнении команды
                HookLog::create([
                    'ip_address' => $ipAddress,
                    'action' => 'Git update failed',
                    'details' => json_encode(['error' => $e->getMessage()]),
                ]);

                return response()->json(['error' => 'Произошла ошибка', 'details' => $e->getMessage()], 500);
            } finally {
                // Убираем блокировку после завершения операции
                Cache::forget('git_update_lock');
            }
        } else {
            // Логирование попытки с неправильным ключом
            HookLog::create([
                'ip_address' => $request->ip(),
                'action' => 'Git hook failed',
                'details' => json_encode(['error' => 'Invalid secret key']),
            ]);

            return response()->json(['error' => 'Неверный секретный ключ'], 403);
        }
    }

    private function executeGitCommands()
    {
        $commands = [
            'git checkout main',
            'git reset --hard',
            'git pull origin main',
        ];

        $output = [];
        foreach ($commands as $command) {
            $process = Process::run($command);  // Используем правильный метод из Laravel

            if ($process->failed()) {
                throw new ProcessFailedException($process);
            }

            $output[] = $process->output();
        }

        return $output;
    }
}
