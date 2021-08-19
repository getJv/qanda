<?php
namespace App\Console\Commands\Interactive;

use Illuminate\Console\Command;
use App\Models\User;

class ListMenu extends AbstractMenuItem
{
    protected $title = "List of questions";

    protected function register(){

    }

    protected function execute(){
        $this->cmd->generateTitle($this->title);
        list($headers,$rows) = $this->listOfQuestionAndAnswers();
        $this->cmd->table($headers,$rows);
        $this->cmd->next();
    }

    /**
     * Provide a list of questions and answer
     * in table format for Qanda command
     *
     * @return array
     */
    private function listOfQuestionAndAnswers(): array
    {

        $headers = ['Question',"Answer"];
        $rows = [];
        foreach ($this->user->questions->fresh() as $question){
            $rows[] = [
                $question->title,
                $question->answer,
            ];
        }

        return [$headers,$rows];
    }
}
