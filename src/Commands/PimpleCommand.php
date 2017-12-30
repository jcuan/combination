<?php

namespace Star\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Star\Core\App;
use JBZoo\PimpleDumper\PimpleDumper;

class PimpleCommand extends Command
{
    protected function configure()
    {

        $this
            ->setName('app:dump-pimple')
            ->setDescription('dump components in pimple container')
            ->setHelp('get pimple.json for type hinting in Phpstorm');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container=App::$container;
        $container->register(new PimpleDumper());
        $dumper = new PimpleDumper();
        $dumper->setRoot(BASE_PATH);
        $dumper->dumpPimple($container);
        $dumper->dumpPhpstorm($container);
        $output->writeln('successfully dump .phpstorm.meta.php and pimple.json to '.APP_PATH);
    }
}