<?php
/**
 * SPT software - Request CLI
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: All function work with Request by CLI parameters
 * 
 */

namespace SPT\Request;

class Cli extends Base
{
	public $args = array();
	private $executable = array();
    
    public function __construct(?array $source = null)
    {
		$argv = $_SERVER['argv'] ?? [];

		$this->executable = array_shift($argv);

		$out = array();

		for ($i = 0, $j = \count($argv); $i < $j; $i++)
		{
			$arg = $argv[$i];

			// --foo --bar=baz
			if (substr($arg, 0, 2) === '--')
			{
				$eqPos = strpos($arg, '=');

				// --foo
				if ($eqPos === false)
				{
					$key = substr($arg, 2);

					// --foo value
					if ($i + 1 < $j && $argv[$i + 1][0] !== '-')
					{
						$value          = $argv[$i + 1];
						$i++;
					}
					else
					{
						$value          = isset($out[$key]) ? $out[$key] : true;
					}

					$out[$key]          = $value;
				}

				// --bar=baz
				else
				{
					$key                = substr($arg, 2, $eqPos - 2);
					$value              = substr($arg, $eqPos + 1);
					$out[$key]          = $value;
				}
			}
			// -k=value -abc
			elseif (substr($arg, 0, 1) === '-')
			{
				// -k=value
				if (substr($arg, 2, 1) === '=')
				{
					$key                = substr($arg, 1, 1);
					$value              = substr($arg, 3);
					$out[$key]          = $value;
				}
				// -abc
				else
				{
					$chars              = str_split(substr($arg, 1));

					foreach ($chars as $char)
					{
						$key            = $char;
						$value          = isset($out[$key]) ? $out[$key] : true;
						$out[$key]      = $value;
					}

					// -a a-value
					if ((\count($chars) === 1) && ($i + 1 < $j) && ($argv[$i + 1][0] !== '-'))
					{
						$out[$key]      = $argv[$i + 1];
						$i++;
					}
				}
			}
			
			$this->args[] = $arg;
		}

		$this->data = $out; 
    }

	public function getArgs()
	{
		return $this->args ?? [];
	}
}
