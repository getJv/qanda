<?php
namespace App\Console\Commands\Interactive;

use Illuminate\Console\Command;
use App\Models\User;

class MainMenu extends AbstractMenuItem
{
    protected $title = "Main Menu";

    public function execute(){
        $this->cmd->generateTitle($this->title);
        $menuOption = [
            ['method' => 'CreateMenu', 'title' =>'Create a question'],
            ['method' => 'ListMenu', 'title' =>'List all questions'],
            ['method' => 'PracticeMenu', 'title' =>'Practice'],
            ['method' => 'StatsMenu', 'title' =>'Stats'],
            ['method' => 'ResetMenu', 'title' =>'Reset'],
        ];
        $answer = $this->cmd->generateChoiceQuestion('Select an option',$menuOption);
        $this->cmd->callNextMenu($menuOption,$answer);
    }
}
