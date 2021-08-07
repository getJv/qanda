<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\MenuItem\AsciiArtItem;

class InteractiveCommand extends Command
{
    protected $signature = 'qanda:interactive';
    protected $description = 'The Interactive Q&A app.';
    private $title = '';
    private $items = '';
    private $art = <<<ART
        _ __ _
       / |..| \
       \/ || \/
        |_''_|
   Welcome to QAnda
The Interactive Q&A app
ART;



    public function __construct()
    {
        parent::__construct();
        list($this->title,$this->items) = $this->welcome();
    }

    private function welcome(){
        return [
            "Main Menu",
            [
                "create" => ["Create","create"],
                "list"   => "List all questions",
                "stats"  => "Show stats",
                "reset"  => "Reset answers",
            ]
        ];
    }
    private function create($menu){
        return function (CliMenu $cliMenu) use ($menu){
            $questionTitle = $cliMenu->askText()
                ->setValidator(function ($title) {
                    return !empty($title);
                })
                ->setPromptText('Type the question title: (enter)')
                ->setValidationFailedText('Please, info the question title')
                ->ask();

            $questionAnswer = $cliMenu->askText()
                ->setValidator(function ($title) {
                    return !empty($title);
                })
                ->setPromptText('Type the question answer: (enter)')
                ->setValidationFailedText('Please, info the question answer')
                ->ask();

            ;
            //$questionAnswer->fetch();
            //$questionTitle->fetch();
            $cliMenu->confirm('Question added!')
                ->display('OK!');

            $menu->setResult('welcome');
            $cliMenu->close();
        };
    }


    private function render(){

        $menu = $this->menu();
        $menu->addAsciiArt($this->art, AsciiArtItem::POSITION_CENTER);
        $menu->addLineBreak('',1);
        $menu->addLineBreak('*-',1);
        $menu->addStaticItem($this->title);
        $menu->addLineBreak('--',1);
        $menu->addLineBreak('',1);
        foreach ($this->items as $key => $value){

            if(is_array($value)){
                $method = $value[1];
                $menu->addItem($value[0], $this->$method($menu));
            }else{
                $menu->addOption($key, $value);
            }

        }

        return $menu
            ->setForegroundColour('42','yellow')
            ->setBackgroundColour('90','black')
            ->setWidth(200)
            ->setPadding(10)
            ->setMarginAuto()
            ->addLineBreak('',1)
            ->addLineBreak('*-',1)
            ->open();
    }


    public function handle()
    {
        while(true){
            $option = $this->render();
            if(!is_string($option)){
                $this->info("Thank you for using " . strtolower($this->description) );
                exit;
            }
            list($this->title,$this->items) = $this->$option();
       }




    }
}
