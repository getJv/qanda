<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PhpSchool\Terminal\IO\BufferedOutput;
use PhpSchool\Terminal\Terminal;
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
        $cmd = $this->artisan('qanda:interactive');
        list($header,$rows) = $this->generateTitle('Welcome to QAnda Interactive app');
        $cmd->expectsTable($header,$rows);
        $cmd->expectsQuestion('Select an user', $chosen);
        $user = User::where("name",$chosen)->first();
        return [$cmd,$user];
}
    private function exitStep($cmd){
        $cmd->expectsOutput('Thank you for use our app!');
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
            /*->expectsQuestion('What is your name?', 'Taylor Otwell')
            ->expectsQuestion('Which language do you prefer?', 'PHP')
            ->expectsOutput('Your name is Taylor Otwell and you prefer PHP.')
            ->doesntExpectOutput('Your name is Taylor Otwell and you prefer Ruby.')
            ->expectsConfirmation('Do you really wish to run this command?', 'no')
            ->expectsTable([
                    'ID',
                    'Email',
                ], [
                    [1, 'taylor@example.com'],
                    [2, 'abigail@example.com'],
                ])
            ->assertExitCode(1);*/
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
        $questionTitle = 'What is you favorite color?';
        $questionAnswer = 'Green';
        list ($cmd,$user) = $this->selectUserStep($selectedUser);
        $this->selectMainMenuStep($cmd,$user,"Create a question");
        $this->createQuestionStep($cmd,$user,'Main menu',$questionTitle,$questionAnswer);
    }
}
