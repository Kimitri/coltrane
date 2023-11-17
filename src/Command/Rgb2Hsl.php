<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Input\InputInterface;

use Spatie\Color\Rgb;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;

class Rgb2Hsl extends AbstractCommand {
  /**
   * Configures the command
   */
	public function configure(): void {
		$this->setName('rgb2hsl')
		    ->setDescription('Convert rgb color values to hsl.')
		    ->setHelp('Converts rgb color values (e.g. "rgb(121,14,212)") to decimal hsl values (e.g. "hsl(207,22%,10%)").')
		    ->addDefaultOptions();
	}

  /**
   * Transforms the input source code.
   *
   * @param  InputInterface $input  Command input.
   * @param  string         $source Input source code.
   * @return string                 Transformed source code.
   */
	public function transform(InputInterface $input, string $source): string {
		return preg_replace_callback(Regex::RGB, function(array $matches) use ($input) {
			if (count($matches) > 1) {
				$color = Rgb::fromString('rgb(' . $matches[1] . ')');
				$aligned = $this->applyPalette($input, $color);
				
				return $aligned->toHsl();
			}

			return $matches[0];
		}, $source);
	}
}
