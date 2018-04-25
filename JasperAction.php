<?php

namespace ddroche\jasperactions;

use chrmorandi\jasper\Jasper;
use yii;
use yii\base\Action;
use yii\helpers\FileHelper;

class JasperAction extends Action
{
    public $report;
    public $attachmentName;
    public $resourceDirectory = '@app/reports';
    public $parameters = [];

    // 'pdf', 'rtf', 'xls', 'xlsx', 'docx', 'odt', 'ods', 'pptx', 'csv', 'html', 'xhtml', 'xml', 'jrprint'
    public function run($format = null)
    {
        /* @var $jasper Jasper */
        $db = Yii::$app->db;
        $jasper = new \ddroche\jasperactions\Jasper([
            'redirect_output' => false,
            'resource_directory' => false,
            'locale' => 'pt_BR',
            'db' => [
                'dsn' => $db->dsn,
                'username' => $db->username,
                'password' => $db->password,
            ]
        ]);

        $output = Yii::getAlias('@runtime/yii2-jasper');

        FileHelper::createDirectory($output);

        if (!isset($format)) {
            $format = Yii::$app->response->format;
        }

        $jasper->process(
            Yii::getAlias($this->resourceDirectory) . "/$this->report",
            $this->parameters,
            $format,
            $output
        )->execute();

        $this->report = explode('.', $this->report)[0];

        Yii::$app->response->sendFile("$output/$this->report.$format", $this->attachmentName, ['inline' => false]);
        //FileHelper::unlink("$output/$this->report.$format");
    }
}
