<?php
namespace App\Console\Commands\Interactive;

use Illuminate\Console\Command;
use App\Models\User;

class ResetMenu extends AbstractMenuItem
{
    protected $title = "Reset answers operation";

    protected function execute(){
        $this->cmd->generateTitle($this->title);
        $confirmed = $this->cmd->confirm('Are you sure you want reset your answers? (irreversible!)');
        if ($confirmed){
            $this->user->questions->each->update(['last_answer' => null]);
            $this->cmd->line('Answers were erased!');
        }
        $this->cmd->generateContextMenu();
    }
}
