<?php

// app/Http/Controllers/GitHubWebhookController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GitHubWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Логирование полученных данных для отладки
        Log::info('Received GitHub webhook: ', $request->all());

        // Проверка типа события
        $event = $request->header('X-GitHub-Event');

        if ($event === 'push') {
            // Реакция на push событие
            Log::info('GitHub push event detected');
            // Здесь можно выполнить действия, например, запустить git pull или другие команды
            $this->performGitPull();
        }

        return response()->json(['message' => 'Webhook received successfully'], 200);
    }

    protected function performGitPull()
    {
        // Вызов команды git pull или других команд
        $process = new \Symfony\Component\Process\Process(['git', 'pull', 'origin', 'main']);
        $process->setWorkingDirectory(base_path()); // Указываем рабочую директорию для git
        $process->run();

        if (!$process->isSuccessful()) {
            Log::error('Git pull failed: ' . $process->getErrorOutput());
        } else {
            Log::info('Git pull completed: ' . $process->getOutput());
        }
    }
}
