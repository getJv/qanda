<?php
namespace App\Console\Commands\Interactive;

use Illuminate\Console\Command;
use App\Models\User;

class MainMenu extends AbstractMenuItem
{
    protected $title = "Main Menu";

    protected function register(){
        $this->cmd->menuRegister('MainMenu', [
            ['method' => 'CreateMenu', 'title' =>'Create a question'],
            ['method' => 'ListMenu', 'title' =>'List all questions'],
            ['method' => 'PracticeMenu', 'title' =>'Practice'],
            ['method' => 'StatsMenu', 'title' =>'Stats'],
            ['method' => 'ResetMenu', 'title' =>'Reset'],
        ]);
    }

    public function execute(){
        $this->register();
        $this->cmd->generateTitle($this->title);
        $answer = $this->cmd->generateChoiceQuestion('Select an option','MainMenu');
        $this->cmd->next('MainMenu',$answer);
    }


}
