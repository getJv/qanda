<?php

namespace App\Console\Commands\Interactive;
use Illuminate\Console\Command;

abstract class AbstractMenuItem
{
    protected $title = "";
    protected $cmd = null;
    protected $user = null;


    public function __construct(Command $cmd)
    {
        $this->cmd = $cmd;
        $this->user = $cmd->getUser();
        $this->execute();
    }

    protected abstract function execute();
}
