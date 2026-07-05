<?php

namespace App\Traits;

trait CanExportData
{
    /**
     * Export data to native CSV format.
     */
    protected function exportToCsv(string $filename, array $headers, array $rows)
    {
        $file = fopen('php://temp', 'r+');
        
        // Add BOM for proper Excel UTF-8 decoding
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        // Headers
        fputcsv($file, $headers);

        // Rows
        foreach ($rows as $row) {
            fputcsv($file, $row);
        }

        rewind($file);
        $csv = stream_get_contents($file);
        fclose($file);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /**
     * Export data to native Excel-compatible HTML format.
     * Excel natively reads this with correct columns, headers and styles.
     */
    protected function exportToExcel(string $filename, string $title, array $headers, array $rows)
    {
        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $html .= '<head>';
        $html .= '<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />';
        $html .= '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Sheet1</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
        $html .= '<style>';
        $html .= 'table { border-collapse: collapse; width: 100%; }';
        $html .= 'th { background-color: #3b82f6; color: white; font-weight: bold; border: 1px solid #d1d5db; padding: 8px; }';
        $html .= 'td { border: 1px solid #d1d5db; padding: 8px; }';
        $html .= '.title { font-size: 16pt; font-weight: bold; margin-bottom: 10px; }';
        $html .= '</style>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<div class="title">' . htmlspecialchars($title) . '</div>';
        $html .= '<table>';
        $html .= '<thead><tr>';
        foreach ($headers as $header) {
            $html .= '<th>' . htmlspecialchars($header) . '</th>';
        }
        $html .= '</tr></thead>';
        $html .= '<tbody>';
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . htmlspecialchars((string) $cell) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</body>';
        $html .= '</html>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xls\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }
}
