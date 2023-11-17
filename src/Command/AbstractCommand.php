<?php
/**
 * This is the base class for all Coltrane color commands.
 *
 * @author Kimmo Tapala <kimitri@gmail.com>
 *
 * This class provides all the common functionality required by the color
 * transformation commands.
 */

namespace Coltrane\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Spatie\Color\Color;

use Coltrane\Palette;

abstract class AbstractCommand extends Command {
	protected $palette = null;
	
	/**
	 * Defines the default command line options.
	 */
	protected function addDefaultOptions() {
		return $this
			->addOption('infile', 'i', InputOption::VALUE_OPTIONAL, 'Input file')
			->addOption('outfile', 'o', InputOption::VALUE_OPTIONAL, 'Output file')
			->addOption('palette', 'p', InputOption::VALUE_OPTIONAL, 'Palette name or file. Use the <info>palettes</> command to list built-in palettes');
	}

	/**
	 * Defines the alpha option used with hsla and rgba colors.
	 */
	protected function addAlphaOption() {
		return $this->addOption('alpha', 'a', InputOption::VALUE_OPTIONAL, 'Alpha value (between 0.0 and 1.0) or component (r, g, b or a) to use as alpha', 1);
	}

	/**
	 * Reads the input source code.
	 *
	 * Input source can be read from a file (defined using the --infile option)
	 * but by default it's read from the standard input.
	 * 
	 * @param  InputInterface $input Command input.
	 * @return string                Input source code.
	 */
	protected function readSource(InputInterface $input) {
		$infile = !empty($input->getOption('infile')) ? $input->getOption('infile') : null;
		$inputStream = ($input instanceof StreamableInputInterface) ? $input->getStream() : STDIN;

		$stream = $infile ? fopen($infile, 'r') : $inputStream;
		$source = '';

		if ($stream) {
			$source = stream_get_contents($stream);
		}

		if ($infile) {
			fclose($stream);
		}

		return $source;
	}

	/**
	 * Writes the output.
	 * 
	 * @param  InputInterface  $input  Command input.
	 * @param  OutputInterface $output Command output.
	 * @param  string          $result Resulting string to write to output.
	 * @return int                  	 Number of bytes written.
	 */
	protected function writeResult(InputInterface $input, OutputInterface $output, string $result) {
		$outfile = !empty($input->getOption('outfile')) ? $input->getOption('outfile') : null;
		$stream = $outfile ? fopen($outfile, 'w') : $output->getStream();

		$bytes = fwrite($stream, $result);

		if ($outfile) {
			fclose($stream);
		}

		return $bytes;
	}

	/**
   * {@inheritdoc}
   */
	public function execute(InputInterface $input, OutputInterface $output) {
		$source = $this->readSource($input);
		$result = $this->transform($input, $source);

		$this->writeResult($input, $output, $result);
		return Command::SUCCESS;
	}

	/**
	 * Performs the color transformation.
	 * 
	 * @param  InputInterface $input  Command input.
	 * @param  string         $source Source code.
	 * @return string                 Source after transformations.
	 */
	public function transform(InputInterface $input, string $source) {
		return $source;
	}

	/**
	 * Applies palette alignment to the input color.
	 * 
	 * @param  InputInterface 	$input Command input.
	 * @param  Color          	$color Color to align to palette.
	 * @return Spatie\Color     Color from palette.
	 */
	public function applyPalette(InputInterface $input, Color $color): Color {
		if ($this->palette == null) {
			$palette_opt = !empty($input->getOption('palette')) ? $input->getOption('palette') : null;

			if ($palette_opt == null) {
				return $color;
			}

			$palette_file = $palette_opt;
			if (file_exists(COLTRANE_ROOT . '/palettes/' . $palette_opt)) {
				$palette_file = COLTRANE_ROOT . '/palettes/' . $palette_opt;
			}

			if (!file_exists($palette_file)) {
				return $color;
			}

			$this->palette = new Palette($palette_file);
		}

		return $this->palette->nearest($color);
	}

	/**
	 * Gets the desired alpha value.
	 * 
	 * @param  InputInterface 	$input Command input.
	 * @param  Color          	$color Color.
	 * @return float 									 Alpha value (0..1).
	 */
	public function alpha(InputInterface $input, Color $color) {
		$methods = [
			'r' => 'red',
			'g' => 'green',
			'b' => 'blue',
			'a' => 'alpha'
		];
		$alpha = strtolower($input->getOption('alpha'));
		if (in_array($alpha, ['r', 'g', 'b', 'a'])) {
			$method = $methods[$alpha];
			$divisor = $alpha == 'a' ? 1 : 255;
			$alpha = $color->$method() / $divisor;
		}

		return min(1, abs($alpha));
	}
}
