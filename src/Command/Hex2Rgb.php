<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Input\InputInterface;

use Spatie\Color\Hex;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;
use Coltrane\StringManipulator;

class Hex2Rgb extends AbstractCommand {
  /**
   * Configures the command
   */
	public function configure(): void {
		$this->setName('hex2rgb')
		    ->setDescription('Convert hexadecimal color values to rgb.')
		    ->setHelp('Converts hexadecimal color values (e.g. "#3d4" or "#2d4e6f") to decimal rgb values (e.g. "rgb(121,14,212)").')
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
		return preg_replace_callback(Regex::HEX, function(array $matches) use ($input) {
			if (count($matches) > 1) {
				$value = substr($matches[1], 1);
				$value = (strlen($value) < 4) ? StringManipulator::dupeChars($value) : $value;

				$color = Hex::fromString('#' . $value);
				$aligned = $this->applyPalette($input, $color);
				
				return $aligned->toRgb();
			}

			return $matches[0];
		}, $source);
	}
}
