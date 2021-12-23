<?php

namespace Huid\PhpcDep\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Huid\PhpcDep\Support;

class UpdateCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('update')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the package')
            ->setDescription('clear package cache and pull new one');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        
        $cacheFilePath = Support\get_cache_file_path($name);
        Support\wrap_gray_formatter($output);
        
        if (is_file($cacheFilePath)) {
            \unlink($cacheFilePath);
        }
        
        $output->writeln("<gray>===> Fetching release list...<gray>");
        $output->writeln("");
        Support\render_packages($name, $input, $output);

        return 0;
    }

}
