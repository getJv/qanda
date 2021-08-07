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

    public function setUp() : void
    {
        $this->output = new BufferedOutput;
        $this->terminal = $this->createMock(Terminal::class);

        /*$this->terminal->expects($this->any())
            ->method('isInteractive')
            ->willReturn(true);

        $this->terminal->expects($this->any())
            ->method('getWidth')
            ->willReturn(48);

        $this->terminal->expects($this->any())
            ->method('write')
            ->will($this->returnCallback(function ($buffer) {
                $this->output->write($buffer);
            }));*/
    }

    /** @test */
    public function tt(){

        $this->artisan('qanda:interactive');
            //->expectsQuestion('What is your name?', 'Taylor Otwell')
           // ->expectsQuestion('Which language do you prefer?', 'PHP');
            //->expectsOutput('Your name is Taylor Otwell and you prefer PHP.')
            //->doesntExpectOutput('Your name is Taylor Otwell and you prefer Ruby.')
            //->assertExitCode(0);
    }
}
