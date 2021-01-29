<?php

namespace WHMCS\Module\Registrar\Dondominio\Cli;

use WHMCS\Module\Registrar\Dondominio\Cli\Output;

class Arguments
{
    /**
     * Flags.
     * @var array
     */
    protected $flags = array();

    /**
     * Options.
     * @var array
     */
    protected $options = array();

    /**
     * Parsed arguments.
     * @var array
     */
    protected $parsed_arguments = array();

    /**
     * Add a flag.
     * @param string|array $flag Flag to add
     * @param string $description Description for the help screen
     */
    public function addFlag($flag, $description = "")
    {
        $this->flags[] = array('flag'=>$flag, 'description'=>$description);
    }

    /**
     * Add a new option.
     */
    public function addOption($option, $default = null, $description = "", $required = false)
    {
        $this->options[] = array('option'=>$option, 'default'=>$default, 'description'=>$description);
    }

    /**
     * Parse arguments from $_SERVER['argv'].
     */
    public function parse()
    {
        $args = $_SERVER['argv'];

        foreach ($args as $arg_key=>$argument) {
            $argument = str_replace("-", "", $argument);

            foreach ($this->flags as $flag) {
                if (!is_array($flag['flag'])) {
                    $flag['flag'] = array($flag['flag']);
                }

                foreach ($flag['flag'] as $subflag) {
                    if ($argument == $subflag) {
                        $this->parsed_arguments[$flag['flag'][0]] = true;
                        break;
                    }
                }
            }

            foreach ($this->options as $option) {
                if (!is_array($option['option'])) {
                    $option['option'] = array($option['option']);
                }

                foreach ($option['option'] as $suboption) {
                    if ($argument == $suboption) {
                        $value = $args[$arg_key+1];

                        if (substr($value, 0, 1) == "-") {
                            $value = $option['default'];
                        }

                        $this->parsed_arguments[$option['option'][0]] = $value;
                        break;
                    }
                }
            }
        }
    }

    /**
     * Get the value of a parameter.
     * @param string $key Parameter name
     * @return string
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->parsed_arguments)) {
            return $this->parsed_arguments[$key];
        }

        foreach ($this->options as $option) {
            if (!is_array($option['option'])) {
                $option['option'] = array($option['option']);
            }

            foreach ($option['option'] as $suboption) {
                if ($suboption == $key) {
                    return $option['default'];
                }
            }
        }

        return null;
    }

    /**
     * Display the help screen.
     */
    public function helpScreen()
    {
        Output::line("Usage: php import.php --username USERNAME --password PASSWORD -uid CLIENTID");
        Output::line("");

        Output::line("Parameters:");

        foreach ($this->options as $option) {
            $options = $this->paramsToString($option['option']);

            Output::line(
                str_pad("  " . $options, 30, " ") . $option['description']
            );
        }

        Output::line("");
        Output::line("Flags:");

        foreach ($this->flags as $flag) {
            $flags = $this->paramsToString($flag['flag']);

            Output::line(
                str_pad("  " . $flags, 30, " ") . $flag['description']
            );
        }
    }

    public function paramsToString($params)
    {
        if (!is_array($params)) {
            return strlen($params) == 1 ? '-' . $params : '--' . $params;
        }

        $formattedParams = array_map(function($param) {
            return strlen($param) == 1 ? '-' . $param : '--' . $param;
        }, $params);

        return implode(", ", $formattedParams);
    }
}