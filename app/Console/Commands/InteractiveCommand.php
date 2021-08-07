<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\MenuItem\AsciiArtItem;
use Illuminate\Database\QueryException;
use App\Models\User;
use PhpSchool\CliMenu\Style\SelectableStyle;


class InteractiveCommand extends Command
{
    protected $signature = 'qanda:interactive';
    protected $description = 'The Interactive Q&A app.';
    private $user  = null;
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
        list($this->title,$this->items) = $this->welcomeMenu();
    }

    private function ignitionMenu(){
        return [
            "Welcome - System ignition",
            [
                "ignitionOption" => ["First Load the database","ignitionOption"],
            ]
        ];
    }
    private function ignitionOption($menu){
        return function (CliMenu $cliMenu) use ($menu){

            try {
                Artisan::call('migrate --seed');
                $menu->setResult('welcomeMenu');
            }catch(QueryException $e){

                $cliMenu->confirm('Remember to set the database at .env file first!')
                    ->display('OK!');
                $menu->setResult('ignitionMenu');
            }

            $cliMenu->close();
        };
    }
    private function welcomeMenu(){
        try{
            $menuOptions = [];
            $menuOptions['newAccountOption'] = ["Create new Account","newAccountOption"];
            $menuOptions['selectUserOption'] = ["Select a User","selectUserOption"];

            $users = User::all();
            foreach ($users as $user){
                $menuOptions[] = "$user->id - $user->name";
            }
            return [ "Identification Area", $menuOptions];

        }catch (QueryException $e){
            return $this->ignitionMenu();
        }
    }
    private function newAccountOption($menu){
        return function (CliMenu $cliMenu) use ($menu){
            $cliMenu->confirm('Available in the next version')
                ->display('OK!');

            $menu->setResult('welcomeMenu');
            $cliMenu->close();
        };
    }
    private function selectUserOption($menu){
        return function (CliMenu $cliMenu) use ($menu){
            $result = $cliMenu->askNumber()
                ->setPromptText('Enter the User ID')
                ->setValidator(function ($value) {
                    $notEmptyRulePass = !empty($value);
                    $mustExistRulePass = User::where('id',$value)->exists();
                    return $notEmptyRulePass && $mustExistRulePass ;
                })
                ->setValidationFailedText('Invalid age, try again')
                ->ask();
            $this->user = User::find($result->fetch());
            $menu->setResult('MainMenu');
            $cliMenu->close();
        };
    }


    private function mainMenu(){
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
                $menu->addStaticItem('    '. $value);
            }

        }

        $menu->setForegroundColour('42','yellow');
        $menu->setBackgroundColour('90','black');
        $menu->setWidth(200);
        $menu->setPadding(10);
        $menu->setMarginAuto();
        $menu->addLineBreak('',1);
        $menu->addLineBreak('',1);
        $menu->addLineBreak('*-',1);
        if(!is_null($this->user)) {
            $menu->setItemExtra("Current: " . $this->user->name)
                ->addItem('Change User', function (CliMenu $cliMenu) use ($menu) {
                    $menu->setResult('WelcomeMenu');
                    $cliMenu->close();
                },true);
         }
        return $menu->open();

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
