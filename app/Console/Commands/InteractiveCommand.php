<?php

namespace App\Console\Commands;
use App\Console\Commands\Interactive\Ignition;
use Illuminate\Console\Command;
use App\Models\User;

class InteractiveCommand extends Command
{
    protected $signature = 'qanda:interactive {--sequential}';
    protected $description = 'The Interactive Q&A app.';
    private $user  = null;

    private  $menuPipe = [
            'ExitMenu' => 'App\Console\Commands\Interactive\ExitMenu',
            'MainMenu' => 'App\Console\Commands\Interactive\MainMenu',
            'CreateMenu' => 'App\Console\Commands\Interactive\CreateMenu',
            'ListMenu' => 'App\Console\Commands\Interactive\ListMenu',
            'PracticeMenu' => 'App\Console\Commands\Interactive\PracticeMenu',
            'StatsMenu' => 'App\Console\Commands\Interactive\StatsMenu',
            'ResetMenu' => 'App\Console\Commands\Interactive\ResetMenu',
    ];

    public function setUser(User $user){
        $this->user = $user;
    }
    public function getUser(){
        return  $this->user;
    }
    public function generateTitle($title){
        if(!$this->option('sequential')){
            system('clear');
        }
        $name = is_null($this->user) ? '' : $this->user->name;
        $this->table(
            ['Screen', 'Current User'],
            [
                [$title, $name],
            ]
        );
    }
    public function generateChoiceQuestion($title,$menuOptions){
        $menuOptions[99] = ['method' => 'exit', 'title' => 'Exit'];
        $extractOption = function ($item){
            return  $item['title'];
        };
        $options = array_map($extractOption,$menuOptions);
        return $this->choice($title,$options);

    }
    public function callNextMenu($menuOption,$option){
        $menuOption[99] = ['method' => 'ExitMenu', 'title' => 'Exit'];
        $extractMethod = function ($item) use($option){
            return  $item['title'] === $option;
        };

        $next = array_filter($menuOption,$extractMethod);
        $next = array_pop($next);
        $nextMenu = $this->menuPipe[$next['method']];

        new $nextMenu($this,$this->user);
    }
    public function generateContextMenu(){
        $menuOption = [
            ['method' => 'MainMenu', 'title' =>'Go to main menu'],
        ];
        $answer = $this->generateChoiceQuestion('Select an option',$menuOption);
        $this->callNextMenu($menuOption,$answer);
    }
    public function handle()
    {
        new Ignition($this,$this->user);
    }
}
