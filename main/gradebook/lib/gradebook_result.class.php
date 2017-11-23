<?php
/* For licensing terms, see /license.txt */

/**
 * Gradebook results class
 * @author Yannick Warnier
 * @package chamilo.gradebook
 */
class GradeBookResult
{
    private $gradebook_list = array(); //stores the list of exercises
    private $results = array(); //stores the results

    /**
     * constructor of the class
     */
    public function __construct($get_questions=false,$get_answers=false)
    {
    }

    /**
     * Exports the complete report as a CSV file
     * @param	string		Document path inside the document tool
     * @param	integer		Optional user ID
     * @param	boolean		Whether to include user fields or not
     * @return	boolean		False on error
     */
    public function exportCompleteReportCSV($dato)
    {
        $filename = 'gradebook_results_'.gmdate('YmdGis').'.csv';
        if (!empty($user_id)) {
            $filename = 'gradebook_results_user_'.$user_id.'_'.gmdate('YmdGis').'.csv';
        }
        $data = '';
        //build the results
        //titles

        foreach ($dato[0] as $header_col) {
            if (!empty($header_col)) {
                $data .= str_replace("\r\n",'  ',api_html_entity_decode(strip_tags($header_col))).';';
            }
        }

        $data .= "\r\n";
        $cant_students = count($dato[1]);

        for ($i = 0; $i < $cant_students; $i++) {
            $column = 0;
            foreach($dato[1][$i] as $col_name) {
                $data .= str_replace("\r\n",'  ',api_html_entity_decode(strip_tags($col_name))).';';
            }
            $data .="\r\n";
        }

        //output the results
        $len = strlen($data);
        header('Content-type: application/octet-stream');
        header('Content-Type: application/force-download');
        header('Content-length: '.$len);
        if (preg_match("/MSIE 5.5/", $_SERVER['HTTP_USER_AGENT'])) {
            header('Content-Disposition: filename= '.$filename);
        } else {
            header('Content-Disposition: attachment; filename= '.$filename);
        } if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
            header('Pragma: ');
            header('Cache-Control: ');
            header('Cache-Control: public'); // IE cannot download from sessions without a cache
        }
        header('Content-Description: '.$filename);
        header('Content-transfer-encoding: binary');
        echo $data;

        return true;
    }

    /**
     * Exports the complete report as an XLS file
     * @return	boolean|null		False on error
     */
    public function exportCompleteReportXLS($data)
    {
        $filename = 'gradebook-results-'.api_get_local_time().'.xlsx';

        $spreadsheet = new PHPExcel();
        $spreadsheet->setActiveSheetIndex(0);
        $worksheet = $spreadsheet->getActiveSheet();

        $line = 0;
        $column = 0; //skip the first column (row titles)

        //headers
        foreach ($data[0] as $header_col) {
            $worksheet->SetCellValueByColumnAndRow($line, $column, html_entity_decode(strip_tags($header_col)));
            $column++;
        }
        $line++;

        $cant_students = count($data[1]);

        for ($i = 0; $i < $cant_students; $i++) {
            $column = 0;
            foreach ($data[1][$i] as $col_name) {
                $worksheet->SetCellValueByColumnAndRow(
                    $line,
                    $column,
                    html_entity_decode(strip_tags($col_name))
                );
                $column++;
            }
            $line++;
        }

        $file = api_get_path(SYS_ARCHIVE_PATH).api_replace_dangerous_char($filename);
        $writer = new PHPExcel_Writer_Excel2007($spreadsheet);
        $writer->save($file);
        DocumentManager::file_send_for_download($file, true, $filename);
        exit;
    }

    /**
     * Exports the complete report as a DOCX file
     * @param $data The table data
     * @return bool
     */
    public function exportCompleteReportDOC($data)
    {
        $filename = 'gradebook_results_'.api_get_local_time() . '.docx';

        $doc = new \PhpOffice\PhpWord\PhpWord();
        $section = $doc->addSection(['orientation' => 'landscape']);
        $table = $section->addTable();
        $table->addRow();

        for ($i = 0; $i < count($data[0]); $i++) {
            $table->addCell(1750)->addText(strip_tags($data[0][$i]));
        }

        foreach ($data[1] as $dataLine) {
            $table->addRow();

            for ($i = 0; $i < count($dataLine); $i++) {
                $table->addCell(1750)->addText(strip_tags($dataLine[$i]));
            }
        }

        $file = api_get_path(SYS_ARCHIVE_PATH) . api_replace_dangerous_char($filename);
        $doc->save($file, 'Word2007');

        DocumentManager::file_send_for_download($file, true, $filename);
        return true;
    }
}
