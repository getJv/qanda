<?php
namespace App\Console\Commands\Interactive;

use Illuminate\Console\Command;
use App\Models\User;

class ExitMenu extends AbstractMenuItem
{
    public function execute(){
        $this->cmd->info("Thank you for use our app!");
    }
}
