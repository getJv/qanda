<?php
namespace App\Console\Commands\Interactive;


use App\Models\User;


class Ignition extends AbstractMenuItem
{
    protected $title = "Welcome to QAnda Interactive app";

    public function execute(){
        $this->cmd->generateTitle($this->title);
        $options = User::listOfUsers();
        $ans = $this->cmd->generateChoiceQuestion('Select an user',$options);
        $user = User::where('name',$ans)->first();
        if(is_null($user)) {
            new ExitMenu($this->cmd);
        }else{
            $this->cmd->setUser($user);
            new MainMenu($this->cmd);
        }
    }
}
