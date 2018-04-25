<?php

namespace ddroche\jasperactions;

use yii\base\Exception;
use yii\helpers\ArrayHelper;

class Jasper extends \chrmorandi\jasper\Jasper
{
    public $dataFile;

    public $jsonQuery = 'contacts.person';

    public function process($input_file, $parameters = [], $format = ['pdf'], $output_file = false)
    {
        if (is_null($input_file) || empty($input_file)) {
            throw new Exception('No input file', 1);
        }

        if (is_array($format)) {
            foreach ($format as $key) {
                if (!in_array($key, $this->formats)) {
                    throw new Exception('Invalid format!', 1);
                }
            }
        } else {
            if (!in_array($format, $this->formats)) {
                throw new Exception('Invalid format!', 1);
            }
        }

        $command = \Yii::getAlias('@vendor') . '/chrmorandi/yii2-jasper/src/JasperStarter/bin/jasperstarter';
        $command .= ' process ';
        $command .= $input_file;

        if ($output_file !== false) {
            $command .= ' -o '.$output_file;
        }

        if (is_array($format)) {
            $command .= ' -f '.implode(' ', $format);
        } else {
            $command .= ' -f '.$format;
        }

        if ($this->resource_directory) {
            $command .= ' -r '.$this->resource_directory;
        }

        if (!empty($this->locale) && $this->locale != null) {
            $parameters = ArrayHelper::merge(['REPORT_LOCALE' => $this->locale], $parameters);
        }

        if (count($parameters) > 0) {
            $command .= ' -P';
            foreach ($parameters as $key => $value) {
                $command .= " '$key'='$value'";
            }
        }

        if (!empty($this->db)) {
            $command .= $this->databaseParams();
        }

        if (!empty($this->dataFile)) {
            $command .= $this->dataFileParams();
        }

        $this->the_command = escapeshellcmd($command);

        return $this;
    }

    /**
     * @return string
     */
    protected function dataFileParams()
    {
        return " -t json --json-query $this->jsonQuery --data-file $this->dataFile";
    }
}
