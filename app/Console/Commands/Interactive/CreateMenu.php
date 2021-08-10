<?php
namespace App\Console\Commands\Interactive;

use Illuminate\Console\Command;
use App\Models\User;

class CreateMenu extends AbstractMenuItem
{
    protected $title = "Create Question";

    protected function execute(){
        $this->cmd->generateTitle($this->title);
        $questionTitle = $this->cmd->ask("What is the question title?");
        $questionAnswer = $this->cmd->ask("What is the question answer?");
        $newQuestion = $this->user->questions()->create(['title' => $questionTitle, 'answer' => $questionAnswer]);
        if(!is_null($newQuestion)){
            $this->cmd->line('Question created!');
        }
        $this->cmd->generateContextMenu();
    }
}
