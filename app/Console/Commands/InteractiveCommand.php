<?php

namespace App\Console\Commands;
use App\Models\Question;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\MenuItem\AsciiArtItem;
use Illuminate\Database\QueryException;
use App\Models\User;
use PhpSchool\CliMenu\Style\SelectableStyle;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;


class InteractiveCommand extends Command
{
    protected $signature = 'qanda:interactive';
    protected $description = 'The Interactive Q&A app.';
    private $user  = null;

    private function generateTitle($title){
        //system('clear');
        $user = is_null($this->user) ? '' : $this->user->name;
        $this->table(
            ['Screen', 'Current User'],
            [
                [$title, $user],
            ]
        );
    }
    private function generateChoiceQuestion($title,$menuOptions){
        $menuOptions[99] = ['method' => 'exit', 'title' => 'Exit'];
        $extractOption = function ($item){
            return  $item['title'];
        };
        $options = array_map($extractOption,$menuOptions);
        return $this->choice($title,$options);

    }
    private function callNextMenu($menuOption,$option){
        $menuOption[99] = ['method' => 'exit', 'title' => 'Exit'];
        $extractMethod = function ($item) use($option){
            return  $item['title'] === $option;
        };

        $next = array_filter($menuOption,$extractMethod);
        $next = array_pop($next);
        $nextMethod = $next['method'];
        $this->$nextMethod();
    }
    private function generateContextMenu(){
        $menuOption = [
            ['method' => 'mainMenu', 'title' =>'Go to main menu'],
        ];
        $answer = $this->generateChoiceQuestion('Select an option',$menuOption);
        $this->callNextMenu($menuOption,$answer);
    }
    private function exit(){
        $this->user = null;
        $this->info("Thank you for use our app!");

    }
    private function ignition(){
        $this->generateTitle('Welcome to QAnda Interactive app');
        $options = User::listOfUsers();
        $ans = $this->generateChoiceQuestion('Select an user',$options);
        $this->user = User::where('name',$ans)->first();
        if(is_null($this->user)) {
            $this->exit();
        }else{
            $this->mainMenu();
        }


    }
    private function mainMenu(){
        $this->generateTitle("Main Menu");
        $menuOption = [
            ['method' => 'create', 'title' =>'Create a question'],
            ['method' => 'list', 'title' =>'List all questions'],
            ['method' => 'practice', 'title' =>'Practice'],
            ['method' => 'stats', 'title' =>'Stats'],
            ['method' => 'reset', 'title' =>'Reset'],
        ];
        $answer = $this->generateChoiceQuestion('Select an option',$menuOption);
        $this->callNextMenu($menuOption,$answer);
    }
    private function create(){
        $this->generateTitle("Create Question");
        $questionTitle = $this->ask("What is the question title?");
        $questionAnswer = $this->ask("What is the question answer?");
        $newQuestion = $this->user->questions()->create(['title' => $questionTitle, 'answer' => $questionAnswer]);
        if(!is_null($newQuestion)){
            $this->line('Question created!');

        }
        $this->generateContextMenu();
    }
    private function list(){
        $this->generateTitle("List of questions");
        list($headers,$rows) = $this->user->listOfQuestionAndAnswers();
        $this->table($headers,$rows);
        $this->generateContextMenu();
    }
    private function stats(){
        $this->generateTitle("Stats and Score");
        list($message,
            $totalOfQuestion,
            $totalOfCorrectAnswers,
            $totalOfInCorrectAnswers,
            $totalOfNotAnswers
            ) = $this->user->questionStats();
        $this->table(
            ['Key', 'Description'],
            [
                ['Number of questions',  $totalOfQuestion],
                ['Correct answers'   ,  $totalOfCorrectAnswers],
                ['Incorrect answers' ,  $totalOfInCorrectAnswers],
                ['Not answers' ,  $totalOfNotAnswers],
                ['Summary' ,  $message],
            ]
        );
        $this->generateContextMenu();
    }
    private function reset(){
        $this->generateTitle("Reset answers operation");
        $confirmed = $this->confirm('Are you sure you want reset your answers? (irreversible!)');
        if ($confirmed){
          $this->user->questions->each->update(['last_answer' => null]);
          $this->line('Answers were erased!');
        }
        $this->generateContextMenu();


    }
    private function practice(){
        $this->generateTitle("Practice Session");

        $separator = new TableSeparator;
        list($message) = $this->user->questionStats();
        $footer = [new TableCell($message, ['colspan' => 4])];
        list($headers,$rows) = $this->user->listOfQuestionAndStats();
        array_push($rows,$separator);
        array_push($rows,$footer);
        $this->table($headers,$rows);
        $chosen = intval($this->ask("Type the question ID"));
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
            $answer = $this->ask($question->title);
            $result = ($answer === $question->answer) ? 'C' : 'W';
            $question->update([
                'last_answer' => $result
            ]);
            if($result === 'C'){
                $this->line('You answered is correct!');
            }else{
                $this->line('You answered is incorrect!');
            }
        }
        $this->generateContextMenu();
    }
    public function handle()
    {
        $this->ignition();
    }
}
