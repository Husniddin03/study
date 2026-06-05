<?php

namespace App\Console\Commands\Generate;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ControllerAndViewCreaterCommand extends Command
{
    protected $signature = 'app:create {name}';

    protected $description = 'Create controller and related view';

    public function handle()
    {
        $name = trim($this->argument('name'), '/');

        $status = $this->call('make:controller', [
            'name' => $this->controllerFormat($name),
        ]);

        if ($status) $this->info('Controller ' . $this->controllerFormat($name) . ' created successfully');


        $status = $this->call('make:view', [
            'name' => $this->viewFormat($name),
        ]);

        if ($status) $this->info('View ' . $this->viewFormat($name) . ' created successfully');
    }

    public function controllerFormat($name)
    {
        $name = strtolower($name);
        return ucwords($name, "/") . 'Controller';
    }

    public function viewFormat($name)
    {
        $arr = explode('/', $name);

        array_walk($arr, function (&$value) {
            $value = Str::plural($value);
        });

        $name = implode('/', $arr);

        return strtolower($name) . "/index";
    }
}
