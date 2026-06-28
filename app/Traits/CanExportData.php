<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\StreamedResponse;

trait CanExportData
{
    /**
     * Export data to native CSV format.
     */
    protected function exportToCsv(string $filename, array $headers, array $rows): StreamedResponse
    {
        $responseHeaders = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}.csv",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return new StreamedResponse(function () use ($headers, $rows) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for proper Excel UTF-8 decoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers
            fputcsv($file, $headers);

            // Rows
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        }, 200, $responseHeaders);
    }

    /**
     * Export data to native Excel-compatible HTML format.
     * Excel natively reads this with correct columns, headers and styles.
     */
    protected function exportToExcel(string $filename, string $title, array $headers, array $rows)
    {
        $responseHeaders = [
            'Content-type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}.xls",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($title, $headers, $rows) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head>';
            echo '<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />';
            echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Sheet1</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
            echo '<style>';
            echo 'table { border-collapse: collapse; width: 100%; }';
            echo 'th { background-color: #3b82f6; color: white; font-weight: bold; border: 1px solid #d1d5db; padding: 8px; }';
            echo 'td { border: 1px solid #d1d5db; padding: 8px; }';
            echo '.title { font-size: 16pt; font-weight: bold; margin-bottom: 10px; }';
            echo '</style>';
            echo '</head>';
            echo '<body>';
            echo '<div class="title">' . htmlspecialchars($title) . '</div>';
            echo '<table>';
            echo '<thead><tr>';
            foreach ($headers as $header) {
                echo '<th>' . htmlspecialchars($header) . '</th>';
            }
            echo '</tr></thead>';
            echo '<tbody>';
            foreach ($rows as $row) {
                echo '<tr>';
                foreach ($row as $cell) {
                    echo '<td>' . htmlspecialchars((string) $cell) . '</td>';
                }
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</body>';
            echo '</html>';
        }, 200, $responseHeaders);
    }
}
