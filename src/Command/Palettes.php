<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Palettes extends Command {
  /**
   * Configures the command
   */
  public function configure(): void {
    $this->setName('palettes')
        ->setDescription('List palettes')
        ->setHelp('Lists all built-in palettes.');
  }

  /**
   * Executes the command
   *
   * @param  InputInterface  $input  Command input.
   * @param  OutputInterface $output Command output.
   * @return int                     Command exit status.
   */
  public function execute(InputInterface $input, OutputInterface $output): int {
    $files = scandir(COLTRANE_ROOT . '/palettes');
    $shown = array_filter($files, function($entry) {
      return !str_starts_with($entry, '.');
    });

    $output->writeln(implode("\n", $shown));
    return Command::SUCCESS;
  }
}
