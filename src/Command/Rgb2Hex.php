<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Spatie\Color\Rgb;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;
use Coltrane\StringManipulator;

class Rgb2Hex extends AbstractCommand {
	public function configure() {
		$this->setName('rgb2hex')
		    ->setDescription('Convert rgb color values to hexadecimal.')
		    ->setHelp('Converts decimal rgb values (e.g. "rgb(121,14,212)") to six-digit hexadecimal color values (e.g. "#2d4e6f").')
		    ->addDefaultOptions();
	}

	public function transform(InputInterface $input, string $source) {
		return preg_replace_callback(Regex::RGB, function(array $matches) use ($input) {
			if (count($matches) > 1) {
				$color = Rgb::fromString('rgb(' . $matches[1] . ')');
				$aligned = $this->applyPalette($input, $color);
				
				return $aligned->toHex();
			}

			return $matches[0];
		}, $source);
	}
}