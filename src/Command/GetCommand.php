<?php

namespace Huid\PhpcDep\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Huid\PhpcDep\Support;

class GetCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('get')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the package')
            ->setDescription('get by input package name')
            ->setHelp('get package version with php version');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        $name = $input->getArgument('name');
        Support\write_get_command_readme($output, $name);
        Support\render_packages($name, $input, $output);

        return 0;
    }

}
