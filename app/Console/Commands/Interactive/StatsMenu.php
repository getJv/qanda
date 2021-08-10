<?php
namespace App\Console\Commands\Interactive;

use Illuminate\Console\Command;
use App\Models\User;

class StatsMenu extends AbstractMenuItem
{
    protected $title = "Stats and Score";

    protected function execute(){
        $this->cmd->generateTitle($this->title);
        list($message,
            $totalOfQuestion,
            $totalOfCorrectAnswers,
            $totalOfInCorrectAnswers,
            $totalOfNotAnswers
            ) = $this->user->questionStats();
        $this->cmd->table(
            ['Key', 'Description'],
            [
                ['Number of questions',  $totalOfQuestion],
                ['Correct answers'   ,  $totalOfCorrectAnswers],
                ['Incorrect answers' ,  $totalOfInCorrectAnswers],
                ['Not answers' ,  $totalOfNotAnswers],
                ['Summary' ,  $message],
            ]
        );
        $this->cmd->generateContextMenu();
    }
}
