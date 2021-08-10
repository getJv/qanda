<?php
namespace App\Console\Commands\Interactive;

use Illuminate\Console\Command;
use App\Models\User;

class ListMenu extends AbstractMenuItem
{
    protected $title = "List of questions";

    protected function execute(){
        $this->cmd->generateTitle($this->title);
        list($headers,$rows) = $this->user->fresh()->listOfQuestionAndAnswers();
        $this->cmd->table($headers,$rows);
        $this->cmd->generateContextMenu();
    }
}
