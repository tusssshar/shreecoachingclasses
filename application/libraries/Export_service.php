<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Export_service {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    public function exportExcel($filename, $title, $columns, $rows)
    {
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
        echo $this->renderExportHtml($title, $columns, $rows, false, false);
        exit;
    }

    public function exportPrint($title, $columns, $rows)
    {
        echo $this->renderExportHtml($title, $columns, $rows, true, false);
        exit;
    }

    public function exportPdf($filename, $title, $columns, $rows)
    {
        echo $this->renderExportHtml($title, $columns, $rows, true, true);
        exit;
    }

    protected function renderExportHtml($title, $columns, $rows, $print = false, $pdfMode = false)
    {
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>' . htmlspecialchars($title) . '</title>';
        $html .= '<style>';
        $html .= 'body{font-family:Arial,Helvetica,sans-serif;margin:18px;color:#222;}';
        $html .= 'table{border-collapse:collapse;width:100%;font-size:11px;}';
        $html .= 'th,td{border:1px solid #ccc;padding:6px 8px;text-align:left;vertical-align:top;}';
        $html .= 'th{background:#f5f5f5;font-weight:700;}';
        $html .= 'h1{font-size:18px;margin-bottom:8px;}';
        $html .= '.metadata{margin-bottom:16px;color:#555;}';
        $html .= '@media print{body{margin:0;} table th,table td{border-color:#999;}}';
        if ($print || $pdfMode) {
            $html .= '@media print{table{page-break-inside:auto;} tr{page-break-inside:avoid;page-break-after:auto;}}';
        }
        $html .= '</style>';

        if ($print || $pdfMode) {
            $html .= '<script>window.onload=function(){if(document.location.search.indexOf("print=true")!==-1||document.location.pathname.indexOf("/print")!==-1||document.location.pathname.indexOf("/pdf")!==-1){window.print();}};</script>';
        }

        $html .= '</head><body>';
        $html .= '<h1>' . htmlspecialchars($title) . '</h1>';

        if ($pdfMode) {
            $html .= '<div class="metadata">This is a PDF-friendly export page. Use your browser\'s print/save dialog to save as PDF.</div>';
        }

        $html .= '<table><thead><tr>';
        foreach ($columns as $column) {
            $html .= '<th>' . htmlspecialchars($column['label']) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($columns as $column) {
                $value = isset($row[$column['key']]) ? $row[$column['key']] : '';
                $html .= '<td>' . htmlspecialchars($value) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        return $html;
    }
}
