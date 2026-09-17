<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeControllerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:controller {name : The name of the controller class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller class in app/Controllers/';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $rawName = $this->argument('name');
        
        // Ensure class name ends with Controller if not already provided
        $className = ucfirst($rawName);
        if (! str_ends_with($className, 'Controller')) {
            $className .= 'Controller';
        }

        $namespace = 'App\\Controllers';
        $stubPath = dirname(__DIR__, 3) . '/stubs/controller.stub';
        $targetDir = dirname(__DIR__, 3) . '/app/Controllers';
        $targetFile = "{$targetDir}/{$className}.php";

        if (! file_exists($stubPath)) {
            $this->error("Stub file not found at path: {$stubPath}");
            return Command::FAILURE;
        }

        if (file_exists($targetFile)) {
            $this->error("Controller [{$className}.php] already exists!");
            return Command::FAILURE;
        }

        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $stubContent = file_get_contents($stubPath);
        $fileContent = str_replace(
            ['{{namespace}}', '{{class}}'],
            [$namespace, $className],
            $stubContent
        );

        file_put_contents($targetFile, $fileContent);

        $this->info("Controller [app/Controllers/{$className}.php] created successfully.");

        return Command::SUCCESS;
    }
}
