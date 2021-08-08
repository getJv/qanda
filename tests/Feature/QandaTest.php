<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PhpSchool\Terminal\IO\BufferedOutput;
use PhpSchool\Terminal\Terminal;
use Tests\TestCase;

class QandaTest extends TestCase
{
    /**
     * @var Terminal
     */
    private $terminal;

    /**
     * @var BufferedOutput
     */
    private $output;



    /** @test */
    public function tt(){

        $this->artisan('qanda:interactive')
            ->expectsQuestion('What is your name?', 'Taylor Otwell')
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
            ->assertExitCode(1);
    }
}
