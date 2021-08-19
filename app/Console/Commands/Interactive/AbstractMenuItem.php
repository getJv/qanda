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
        $this->register();
        $this->execute();
    }

    /**
     * Should register the menu options when needed
     * @return mixed
     */
    protected abstract function register();
    /**
     * Should execute the Menu Screen needs
     * @return mixed
     */
    protected abstract function execute();
}
