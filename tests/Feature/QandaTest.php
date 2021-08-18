<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Tests\TestCase;
use App\Models\User;
use App\Models\Question;

class QandaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        if(User::all()->empty()){
            $this->artisan('db:seed',['--class=UserSeeder'] );
        }
    }
    private function generateTitle($title,$user = null){
        $user = is_null($user) ? '' : $user->name;
        $header = [ 'Screen','Current User'];
        $rows   = [[$title, $user]];
        return [$header,$rows];
    }

    private function selectUserStep($chosen): array
    {
        $cmd = $this->artisan('qanda:interactive --sequential');
        list($header,$rows) = $this->generateTitle('Welcome to QAnda Interactive app');
        $cmd->expectsTable($header,$rows);
        $cmd->expectsQuestion('Select an user', $chosen);
        $user = User::where("name",$chosen)->first();
        return [$cmd,$user];
}
    private function exitStep($cmd){
        $cmd->expectsOutput('Thank you for using our app!');
        $cmd->assertExitCode(0);
    }
    private function selectMainMenuStep($cmd,$user,$chosen){
        list($header,$rows) = $this->generateTitle('Main Menu',$user);
        $cmd->expectsTable($header,$rows);
        $cmd->expectsQuestion('Select an option', $chosen);
    }
    private function createQuestionStep($cmd,$user,$chosen,$questionTitle,$questionAnswer){
        list($header,$rows) = $this->generateTitle('Create Question',$user);
        $cmd->expectsTable($header,$rows);
        $cmd->expectsQuestion('What is the question title?', $questionTitle);
        $cmd->expectsQuestion('What is the question answer?', $questionAnswer);
        $cmd->expectsOutput('Question created!');
        $cmd->expectsQuestion('Select an option',$chosen);
    }


    /** @test */
    public function should_exit_from_load_user_screen(){
        list($cmd) = $this->selectUserStep('Exit'); // 99 -> Exit
        $this->exitStep($cmd);
    }
    /** @test */
    public function should_exit_from_main_menu_screen(){
        $selectedUser = 'Jhonatan';
        list($cmd,$user) =  $this->selectUserStep($selectedUser);
        $this->selectMainMenuStep($cmd,$user,"Exit");
        $this->exitStep($cmd);
    }
    /** @test */
    public function should_create_a_question(){
        $selectedUser = 'Jhonatan';
        $questionTitle = 'What is you favorite color?';
        $questionAnswer = 'Green';
        list ($cmd,$user) = $this->selectUserStep($selectedUser);
        $this->selectMainMenuStep($cmd,$user,"Create a question");
        $this->createQuestionStep($cmd,$user,'Exit',$questionTitle,$questionAnswer);
        $this->exitStep($cmd);
    }
    /** @test */
    public function should_list_questions(){
        $selectedUser = 'Jhonatan';
        list ($cmd,$user) = $this->selectUserStep($selectedUser);
        $this->selectMainMenuStep($cmd,$user,"List all questions");
        list($header,$rows) = $this->generateTitle('List of questions',$user);
        $cmd->expectsTable($header,$rows);
        $headers =  ['Question',"Answer"];
        list(,$rows) = $user->listOfQuestionAndAnswers();
        $cmd->expectsTable($headers,$rows);
        $cmd->expectsQuestion('Select an option', 'Exit');
        $this->exitStep($cmd);
    }
    /** @test */
    public function should_practice_questions(){

        /**
         * TODO This test is compete and suppose to be working. BUT
         * the issue: https://github.com/laravel/framework/issues/35805
         * indicates we had a issue with expectsTable when use recursive calls.
         * Please, Investigate and provide solution if possible.
         */

        $selectedUser = 'Jhonatan';
        list ($cmd,$user) = $this->selectUserStep($selectedUser);
        $this->selectMainMenuStep($cmd,$user,"Practice");
        list($header,$rows) = $this->generateTitle('Practice Session',$user);
        $cmd->expectsTable($header,$rows);
        $header =  ['ID', 'Question',"Last answer"];
        list(,$rows) = $user->listOfQuestionAndStats();
        $separator = new TableSeparator;
        list($message) = $user->questionStats();
        $footer = [new TableCell($message, ['colspan' => 3])];
        array_push($rows,$separator);
        array_push($rows,$footer);
        $cmd->expectsTable($header,$rows);
        $selectedQuestionId = $rows[0][0];
        $question = Question::find($selectedQuestionId);
        $selectedQuestionTitle = $question->title;
        $selectedQuestionAnswer = $question->answer;
        $cmd->expectsQuestion('Type the question ID', $selectedQuestionId);
        $cmd->expectsQuestion($selectedQuestionTitle, $selectedQuestionAnswer);
        $cmd->expectsOutput('You answered is correct!');
        $cmd->expectsQuestion('Select an option', 'Exit');
        $this->exitStep($cmd);
    }
    /** @test */
    public function should_show_user_stats(){
        $selectedUser = 'Jhonatan';
        list ($cmd,$user) = $this->selectUserStep($selectedUser);
        $this->selectMainMenuStep($cmd,$user,"Stats");
        list($header,$rows) = $this->generateTitle('Stats and Score',$user);
        $cmd->expectsTable($header,$rows);
        $headers =  ['Key',"Description"];
        list($message,
            $totalOfQuestion,
            $totalOfCorrectAnswers,
            $totalOfInCorrectAnswers,
            $totalOfNotAnswers
            ) = $user->questionStats();
        $cmd->expectsTable($headers,[
            ['Number of questions',  $totalOfQuestion],
            ['Correct answers'   ,  $totalOfCorrectAnswers],
            ['Incorrect answers' ,  $totalOfInCorrectAnswers],
            ['Not answers' ,  $totalOfNotAnswers],
            ['Summary' ,  $message],
        ]);
        $cmd->expectsQuestion('Select an option', 'Exit');
        $this->exitStep($cmd);
    }
    /** @test */
    public function should_clear_questions_answers(){
        $selectedUser = 'Jhonatan';
        list ($cmd,$user) = $this->selectUserStep($selectedUser);
        $this->selectMainMenuStep($cmd,$user,"Reset");
        list($header,$rows) = $this->generateTitle('Reset answers operation',$user);
        $cmd->expectsTable($header,$rows);
        $cmd->expectsConfirmation('Are you sure you want reset your answers? (irreversible!)', 'yes');
        $cmd->expectsOutput('Answers were erased!');
        $cmd->expectsQuestion('Select an option', 'Exit');
        $this->exitStep($cmd);
    }
}
