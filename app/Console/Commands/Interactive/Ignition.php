<?php
namespace App\Console\Commands\Interactive;


use App\Models\User;


class Ignition extends AbstractMenuItem
{
    protected $title = "Welcome to QAnda Interactive app";

    public function register(){
        $users = User::all();
        $userList = [];
        foreach ($users as $user){
            $userList[] = ["method" => '', 'title' => $user->name ];
        }
        $this->cmd->menuRegister('UserList',$userList );
    }

    public function execute(){
        $this->register();
        $this->cmd->generateTitle($this->title);
        $ans = $this->cmd->generateChoiceQuestion('Select an user','UserList');
        $user = User::where('name',$ans)->first();
        if(is_null($user)) {
            new ExitMenu($this->cmd);
        }else{
            $this->cmd->setUser($user);
            new MainMenu($this->cmd);
        }
    }




}
