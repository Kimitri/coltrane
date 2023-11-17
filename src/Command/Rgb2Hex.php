<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Input\InputInterface;

use Spatie\Color\Rgb;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;

class Rgb2Hex extends AbstractCommand {
  /**
   * Configures the command
   */
	public function configure(): void {
		$this->setName('rgb2hex')
		    ->setDescription('Convert rgb color values to hexadecimal.')
		    ->setHelp('Converts decimal rgb values (e.g. "rgb(121,14,212)") to six-digit hexadecimal color values (e.g. "#2d4e6f").')
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
				
				return $aligned->toHex();
			}

			return $matches[0];
		}, $source);
	}
}
