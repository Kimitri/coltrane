<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Input\InputInterface;

use Spatie\Color\Hsl;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;

class Hsl2Hex extends AbstractCommand {
  /**
   * Configures the command
   */
	public function configure(): void {
		$this->setName('hsl2hex')
		    ->setDescription('Convert hsl color values to hexadecimal.')
		    ->setHelp('Converts decimal hsl values (e.g. "hsl(207,22%,10%)") to six-digit hexadecimal color values (e.g. "#2d4e6f").')
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
		return preg_replace_callback(Regex::HSL, function(array $matches) use ($input) {
			if (count($matches) > 1) {
				$color = Hsl::fromString('hsl(' . $matches[1] . ')');
				$aligned = $this->applyPalette($input, $color);
				
				return $aligned->toHex();
			}

			return $matches[0];
		}, $source);
	}
}
