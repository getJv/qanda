<?php
namespace App\Console\Commands\Interactive;

use App\Models\Question;
use Illuminate\Console\Command;
use App\Models\User;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;


class PracticeMenu extends AbstractMenuItem
{
    protected $title = "Practice Session";

    protected function execute(){
        $this->cmd->generateTitle($this->title);
        $separator = new TableSeparator;
        list($message) = $this->user->questionStats();
        $footer = [new TableCell($message, ['colspan' => 4])];
        list($headers,$rows) = $this->user->listOfQuestionAndStats();
        array_push($rows,$separator);
        array_push($rows,$footer);
        $this->cmd->table($headers,$rows);
        $chosen = intval($this->cmd->ask("Type the question ID"));
        $questions = $this->user->questions;
        $mustExistRuleFail = function () use ($questions,$chosen){
            $obj = $questions->first(function($item) use ($chosen){ return $item->id === $chosen; });
            return is_null($obj);
        };
        $cantPickCorrectOneRuleFail = function () use ($questions,$chosen){
            $obj = $questions->first(function($item) use ($chosen){ return $item->id === $chosen; });
            return !is_null($obj) && $obj->last_answer === 'C';
        };
        $mustBeIntegerRuleFail = function () use ($questions,$chosen){
            return $chosen < 1; // intval returns 0 when converts a not number
        };
        if( $mustBeIntegerRuleFail() || $mustExistRuleFail() ||  $cantPickCorrectOneRuleFail() ){
            $this->line("Invalid option. Try again");
        }else{
            $question = Question::find($chosen);
            $answer = $this->cmd->ask($question->title);
            $result = ($answer === $question->answer) ? 'C' : 'W';
            $question->update([
                'last_answer' => $result
            ]);
            if($result === 'C'){
                $this->cmd->line('You answered is correct!');
            }else{
                $this->cmd->line('You answered is incorrect!');
            }
        }
        $this->cmd->generateContextMenu();
    }
}
