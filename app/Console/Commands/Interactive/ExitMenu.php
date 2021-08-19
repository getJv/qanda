<?php
namespace App\Console\Commands\Interactive;

use Illuminate\Console\Command;
use App\Models\User;

class ExitMenu extends AbstractMenuItem
{
    public function register(){

    }

    public function execute(){
        $this->cmd->info("Thank you for using our app!");
    }
}
