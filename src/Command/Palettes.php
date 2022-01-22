<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Palettes extends Command {
  public function configure() {
    $this->setName('palettes')
        ->setDescription('List palettes')
        ->setHelp('Lists all built-in palettes.');
  }

  public function execute(InputInterface $input, OutputInterface $output) {
    $files = scandir(COLTRANE_ROOT . '/palettes');
    $shown = array_filter($files, function($entry) {
      return !str_starts_with($entry, '.');
    });

    $output->writeln(implode("\n", $shown));
    return Command::SUCCESS;
  }
}