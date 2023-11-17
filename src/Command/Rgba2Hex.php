<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Input\InputInterface;

use Spatie\Color\Rgba;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;

class Rgba2Hex extends AbstractCommand {
  /**
   * Configures the command
   */
	public function configure() {
		$this->setName('rgba2hex')
		    ->setDescription('Convert rgba color values to hexadecimal.')
		    ->setHelp('Converts decimal rgba values (e.g. "rgba(121,14,212,0.8)") to six-digit hexadecimal color values (e.g. "#2d4e6f").')
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
		return preg_replace_callback(Regex::RGBA, function(array $matches) use ($input) {
			if (count($matches) > 1) {
				$color = Rgba::fromString('rgba(' . $matches[1] . ')');
				$aligned = $this->applyPalette($input, $color);
				
				return $aligned->toHex();
			}

			return $matches[0];
		}, $source);
	}
}
