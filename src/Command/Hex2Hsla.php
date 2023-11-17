<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Input\InputInterface;

use Spatie\Color\Hex;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;
use Coltrane\StringManipulator;

class Hex2Hsla extends AbstractCommand {
  /**
   * Configures the command
   */
	public function configure(): void {
		$this->setName('hex2hsla')
		    ->setDescription('Convert hexadecimal color values to hsla.')
		    ->setHelp('Converts hexadecimal color values (e.g. "#3d4" or "#2d4e6f") to decimal hsla values (e.g. "hsla(207,22%,10%,0.8)").')
		    ->addDefaultOptions()
		    ->addAlphaOption();
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
				$hsla = $aligned->toHsla();
				$alpha = $this->alpha($input, $hsla);

				return $hsla->toHsla($alpha);
			}

			return $matches[0];
		}, $source);
	}
}
