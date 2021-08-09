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
        $menuOption = [
            ['method' => 'create', 'title' =>'Create another question'],
            ['method' => 'mainMenu', 'title' =>'Main menu'],
        ];
        $answer = $this->generateChoiceQuestion('Select an option',$menuOption);
        $this->callNextMenu($menuOption,$answer);
    }
    private function list(){
        $this->generateTitle("List of Questions");

        $this->table(
            ['ID', 'Question',"Answer","Last answer"],
            $this->user->listOfQuestions()
        );
        $menuOption = [
            ['method' => 'mainMenu', 'title' =>'Go to main menu'],
        ];
        $answer = $this->generateChoiceQuestion($menuOption);
        $this->callNextMenu($menuOption,$answer);
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
        $menuOption = [
            ['method' => 'mainMenu', 'title' =>'Go to main menu'],
        ];
        $answer = $this->generateChoiceQuestion($menuOption);
        $this->callNextMenu($menuOption,$answer);
    }
    private function reset(){
        $this->generateTitle("Reset answers operation");
        $confirmed = $this->confirm('Are you sure you want rest your answers?');
        if ($confirmed){
          $this->user->questions->each->update(['last_answer' => null]);
        }
        $this->mainMenu();


    }
    private function practice(){
        $this->generateTitle("Practice Session");

        $separator = new TableSeparator;
        list($message) = $this->user->questionStats();
        $footerHeader = [new TableCell($message, ['colspan' => 4])];
        $lines = $this->user->fresh()->listOfQuestions();
        array_push($lines,$separator);
        array_push($lines,$footerHeader);
        $this->table(
            ['ID', 'Question',"Answer","Last answer"],
            $lines
        );

        $chosen = intval($this->ask("Choose a question using his ID, or type 99 to Main Menu."));
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

        if ($chosen === 99){
            $this->mainMenu();
        }elseif( $mustBeIntegerRuleFail() || $mustExistRuleFail() ||  $cantPickCorrectOneRuleFail() ){
            $this->line("Invalid option. Try again");
            $this->practice();
        }else{
            $question = Question::find($chosen);
            $answer = $this->ask($question->title);

            $question->update([
                'last_answer' => ($answer === $question->answer) ? 'C' : 'W'
            ]);

            $this->practice();
        }


    }
    public function handle()
    {

        $this->ignition();



        //$this->line('Your name is '.$name.' and you prefer '.$language.'.');

        //$name = $this->ask('Do you really wish to run this command?');

        /*$this->table(
            ['ID', 'Email'],
            [
                [1, 'taylor@example.com'],
                [2, 'abigail@example.com'],
            ]
        );*/

    }
}
