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

    private  $screenPipe = [
            'ExitMenu' => 'App\Console\Commands\Interactive\ExitMenu',
            'MainMenu' => 'App\Console\Commands\Interactive\MainMenu',
            'CreateMenu' => 'App\Console\Commands\Interactive\CreateMenu',
            'ListMenu' => 'App\Console\Commands\Interactive\ListMenu',
            'PracticeMenu' => 'App\Console\Commands\Interactive\PracticeMenu',
            'StatsMenu' => 'App\Console\Commands\Interactive\StatsMenu',
            'ResetMenu' => 'App\Console\Commands\Interactive\ResetMenu',
    ];
    private  $menuMapper = [];
    public function menuRegister($group,$options){
        $this->menuMapper[$group] = $options;
        $this->menuMapper[$group][99] = ['method' => 'ExitMenu', 'title' => 'Exit'];
    }
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
    public function generateChoiceQuestion($title,$menuGroup){

        $extractTitleForOptions = function ($item){
            return  $item['title'];
        };
        $options = array_map($extractTitleForOptions,$this->menuMapper[$menuGroup]);
        return $this->choice($title,$options);
    }
    public function next($caller = null,$option = null){

        if(is_null($caller)){
            $answer = $this->generateChoiceQuestion('Select an option','contextMenu');
            $this->next('contextMenu',$answer);
        }else{

            $menus =  array_filter($this->menuMapper[$caller],function($item) use ($option){
                    return $item['title'] === $option;
            });

            if($option === 'Exit')
                $className =  'ExitMenu';
            else{
                $menu = array_shift($menus);
                $className = $menu['method'];
            }

            $nextMenu = $this->screenPipe[$className];
            new $nextMenu($this,$this->user);
        }
    }
    public function handle()
    {
       $this->menuRegister('contextMenu', [
           ['method' => 'MainMenu', 'title' =>'Go to main menu'],
       ]);
        new Ignition($this,$this->user);
    }
}
