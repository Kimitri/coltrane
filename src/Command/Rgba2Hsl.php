<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Spatie\Color\Rgba;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;
use Coltrane\StringManipulator;

class Rgba2Hsl extends AbstractCommand {
	public function configure() {
		$this->setName('rgba2hsl')
		    ->setDescription('Convert rgba color values to hsl.')
		    ->setHelp('Converts rgba color values (e.g. "rgba(121,14,212,.8)") to decimal hsl values (e.g. "hsl(207,22%,10%)").')
		    ->addDefaultOptions();
	}

	public function transform(InputInterface $input, string $source) {
		return preg_replace_callback(Regex::RGBA, function(array $matches) use ($input) {
			if (count($matches) > 1) {
				$color = Rgba::fromString('rgba(' . $matches[1] . ')');
				$aligned = $this->applyPalette($input, $color);
				
				return $aligned->toHsl();
			}

			return $matches[0];
		}, $source);
	}
}